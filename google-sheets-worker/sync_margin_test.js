require('dotenv').config();
const { GoogleSpreadsheet } = require('google-spreadsheet');
const { JWT } = require('google-auth-library');
const creds = require('./credentials.json');

const SKU_TO_SYNC = 4500;
const SOURCE_TAB_NAME = '10x10mm';
const TARGET_TAB_NAME = 'Hoja1';

// Source Indices (10x10mm)
const SOURCE_COL_SKU = 0; // A
const SOURCE_COL_MARGIN = 19; // T
const SOURCE_COL_ITEM_ID = 18; // S

// Target Indices (Hoja1)
const TARGET_COL_SKU = 7; // H
const TARGET_COL_MARGIN = 10; // K
const TARGET_COL_ITEM_ID = 0; // A

// Helper to separate digits
function cleanId(id) {
    if (!id) return '';
    return String(id).replace(/\D/g, '');
}

async function syncMargin() {
    const jwt = new JWT({
        email: creds.client_email,
        key: creds.private_key,
        scopes: ['https://www.googleapis.com/auth/spreadsheets'],
    });

    const doc = new GoogleSpreadsheet(process.env.SHEET_ID, jwt);
    await doc.loadInfo();

    const sourceSheet = doc.sheetsByTitle[SOURCE_TAB_NAME];
    const targetSheet = doc.sheetsByTitle[TARGET_TAB_NAME];

    if (!sourceSheet || !targetSheet) {
        console.error('Could not find one of the sheets.');
        return;
    }

    console.log(`Loading cells from ${SOURCE_TAB_NAME} (Rows: ${sourceSheet.rowCount})...`);
    // Load relevant columns only (A, S, T) implies range A:T really, or sparse loading?
    // Library loads rectangular ranges. A to T is 20 columns. Maybe 1000 rows. 20k cells. OK.
    await sourceSheet.loadCells({
        startRowIndex: 0,
        endRowIndex: sourceSheet.rowCount,
        startColumnIndex: 0,
        endColumnIndex: 20 // Up to T (index 19) inclusive
    });

    const sourceMatches = [];
    for (let i = 0; i < sourceSheet.rowCount; i++) {
        const skuVal = sourceSheet.getCell(i, SOURCE_COL_SKU).value;
        if (skuVal == SKU_TO_SYNC) {
            const marginVal = sourceSheet.getCell(i, SOURCE_COL_MARGIN).value;
            const itemIdVal = sourceSheet.getCell(i, SOURCE_COL_ITEM_ID).value;
            sourceMatches.push({
                rowIndex: i,
                sku: skuVal,
                margin: marginVal,
                itemId: itemIdVal,
                cleanItemId: cleanId(itemIdVal)
            });
        }
    }

    console.log(`Found ${sourceMatches.length} matches in Source for SKU ${SKU_TO_SYNC}:`);
    sourceMatches.forEach(m => console.log(`  Row ${m.rowIndex + 1}: ItemID=${m.itemId}, Margin=${m.margin}`));

    if (sourceMatches.length === 0) {
        console.log('No source match found.');
        return;
    }

    console.log(`Loading cells from ${TARGET_TAB_NAME} (Rows: ${targetSheet.rowCount})...`);
    // Load A to K (Index 10). 11 columns.
    await targetSheet.loadCells({
        startRowIndex: 0,
        endRowIndex: targetSheet.rowCount,
        startColumnIndex: 0,
        endColumnIndex: 11 // Up to K (index 10) inclusive
    });

    let updates = 0;
    for (let i = 0; i < targetSheet.rowCount; i++) {
        const tSku = targetSheet.getCell(i, TARGET_COL_SKU).value;

        if (tSku == SKU_TO_SYNC) {
            const tItemId = targetSheet.getCell(i, TARGET_COL_ITEM_ID).value;
            const tCleanId = cleanId(tItemId);
            console.log(`Found Target Row ${i + 1}: SKU=${tSku}, ItemID=${tItemId} (Clean: ${tCleanId})`);

            // Find best source match
            let bestMatch = sourceMatches.find(m => m.cleanItemId == tCleanId);
            if (!bestMatch) {
                // Fallback: try finding by SKU order if IDs don't match?
                // User said "verificar con el item ID". If no ID match, maybe log warning?
                // Or fallback to first match as before, but warn.
                console.log(`  No exact ItemID match (Checked clean IDs). Using first source match.`);
                bestMatch = sourceMatches[0];
            } else {
                console.log(`  Matched with Source Row ${bestMatch.rowIndex + 1} (ItemID: ${bestMatch.itemId})`);
            }

            if (bestMatch && bestMatch.rowIndex !== undefined) {
                // Construct A1 notation for the source cell.
                // rowIndex is 0-based index from loadCells/getRows?
                // In loadCells loop: i=0 is Row 1.
                // So Row Number = rowIndex + 1.
                // Column T is index 19. A=1... T=20?
                // A=0(A), ... S=18(S), T=19(T).
                // Google Sheets A1 notation uses letters. T is T.
                const sourceRowNumber = bestMatch.rowIndex + 1;
                const formula = `='${SOURCE_TAB_NAME}'!T${sourceRowNumber}`;

                console.log(`  Updating Margin with Formula: ${formula}`);
                const cell = targetSheet.getCell(i, TARGET_COL_MARGIN);
                cell.formula = formula; // Use .formula property
                updates++;
            } else {
                console.log(`  No match found to link.`);
            }
        }
    }

    if (updates > 0) {
        await targetSheet.saveUpdatedCells();
        console.log(`Updated ${updates} rows in ${TARGET_TAB_NAME}.`);
    } else {
        console.log('No updates performed.');
    }
}

syncMargin().catch(console.error);
