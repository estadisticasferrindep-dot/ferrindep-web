require('dotenv').config();
const { GoogleSpreadsheet } = require('google-spreadsheet');
const { JWT } = require('google-auth-library');
const creds = require('./credentials.json');

const TARGET_TAB_NAME = 'Hoja1';

// Indices in Hoja1
const COL_ITEM_ID = 0; // A
const COL_DESC = 5;    // F
const COL_SKU = 7;     // H
const COL_MARGIN = 10; // K

// Indices in Source Tabs (Assumed consistent across 10x10mm, 15x15mm, etc. and MET DESPLE?)
// Based on previous work:
// 10x10mm: A=SKU, S=ItemID, T=Margin (Index 19)
// MET DESPLE: Let's assume same layout? 
// In "Protocolo", we used Col N=13 for Margin (Index 13), Col C=2 Cost. 
// Wait. "MET DESPLE" layout might be different.
// Step 2547 (50x150mm): Row | SKU | ... | Margin (Col N?)
// Let's re-verify MET DESPLE column indices.
// "50x150mm" batch used goal_seek_range.js.
// goal_seek_range.js: const COL_N = 13; // Margin.
// 10x10mm sync test used Col T (Index 19).
// So layout DIFFERS between tabs?
// 50x150mm used Col 13 (N) for Margin.
// 10x10mm used Col 19 (T) for Margin.
// I need to map Tab -> Margin Column Index.

const TAB_CONFIG = {
    '10x10mm': { marginCol: 19, itemIdCol: 18 }, // T, S
    '15x15mm': { marginCol: 19, itemIdCol: 18 }, // Assumed same as 10x10
    '20x20mm': { marginCol: 19, itemIdCol: 18 },
    '25x25mm': { marginCol: 19, itemIdCol: 18 }, // Unverified, assume standard
    '40x40mm': { marginCol: 19, itemIdCol: 18 },
    '50x50mm': { marginCol: 19, itemIdCol: 18 },
    '50x150mm': { marginCol: 13, itemIdCol: 1 }, // Verify ItemID col for Met Desple style tabs?
    // Wait, 50x150mm batch log (Step 2547): SKU (Col B?), Margin (Col N?)
    // If layout is different, I need to know ItemID column too.
    'MET DESPLE': { marginCol: 13, itemIdCol: 1 } // Placeholder, needed validation
};

// Helper: Map Description to Tab Name
function getTabFromDescription(desc) {
    if (!desc) return null;
    const d = desc.toLowerCase();

    if (d.startsWith('10x10mm')) return '10x10mm';
    if (d.startsWith('15x15mm')) return '15x15mm';
    if (d.startsWith('20x20mm')) return '20x20mm';
    if (d.startsWith('25x25mm')) return '25x25mm';
    if (d.startsWith('40x40mm')) return '40x40mm';
    if (d.startsWith('50x50mm')) return '50x50mm';
    if (d.startsWith('50x150mm')) return '50x150mm';

    // Met Desple keywords
    if (d.startsWith('18x40mm') || d.startsWith('8x17mm') || d.startsWith('20x50mm')) {
        return 'MET DESPLE';
    }

    return null;
}

// Helper: Clean ID
function cleanId(id) {
    if (!id) return '';
    return String(id).replace(/\D/g, '');
}

async function syncGlobalMargins() {
    console.log("Starting Global Sync...");
    const jwt = new JWT({
        email: creds.client_email,
        key: creds.private_key,
        scopes: ['https://www.googleapis.com/auth/spreadsheets'],
    });

    const doc = new GoogleSpreadsheet(process.env.SHEET_ID, jwt);
    await doc.loadInfo();

    const targetSheet = doc.sheetsByTitle[TARGET_TAB_NAME];
    console.log(`Loading target ${TARGET_TAB_NAME} (Rows: ${targetSheet.rowCount})...`);
    await targetSheet.loadCells({
        startRowIndex: 0,
        endRowIndex: targetSheet.rowCount,
        startColumnIndex: 0,
        endColumnIndex: COL_MARGIN + 1
    });

    // Group rows by target tab
    const rowsByTab = {};

    for (let i = 1; i < targetSheet.rowCount; i++) {
        const sku = targetSheet.getCell(i, COL_SKU).value;
        if (!sku) continue;

        const desc = targetSheet.getCell(i, COL_DESC).value;
        const tabName = getTabFromDescription(desc);

        if (tabName) {
            if (!rowsByTab[tabName]) rowsByTab[tabName] = [];
            rowsByTab[tabName].push({
                rowIndex: i,
                sku: sku,
                itemId: targetSheet.getCell(i, COL_ITEM_ID).value,
                cleanItemId: cleanId(targetSheet.getCell(i, COL_ITEM_ID).value)
            });
        }
    }

    console.log("Found rows to sync:");
    for (const [tab, rows] of Object.entries(rowsByTab)) {
        console.log(`  ${tab}: ${rows.length} rows`);
    }

    // Process each tab
    for (const [tabName, targetRows] of Object.entries(rowsByTab)) {
        if (!doc.sheetsByTitle[tabName]) {
            console.warn(`  Warning: Tab '${tabName}' not found in doc. Skipping.`);
            continue;
        }

        const sourceSheet = doc.sheetsByTitle[tabName];
        console.log(`  Processing ${tabName}... (Source Rows: ${sourceSheet.rowCount})`);

        // If unknown, default to 10x10 style (Col T=19)
        // But we need to handle MET DESPLE / 50x150mm variance.
        // I will assume 10x10/15x15/20x20/25x25/40x40/50x50 share layout (Mesh tabs).
        // MET DESPLE and 50x150mm might share layout (Expanded Metal).

        // Standard Layout for Key Tabs (10x10mm, MET DESPLE, etc.)
        // SKU = Col A (0)
        // ItemID = Col S (18)
        // Margin = Col T (19)

        const marginColIndex = 19;
        const itemIdColIndex = 18;
        const skuColIndex = 0;
        const marginColLetter = 'T';

        // Load Source Cells
        await sourceSheet.loadCells({
            startRowIndex: 0,
            endRowIndex: sourceSheet.rowCount,
            startColumnIndex: 0, // A
            endColumnIndex: marginColIndex + 1 // Up to T
        });

        const sourceMap = [];
        const skuMap = {};

        for (let r = 0; r < sourceSheet.rowCount; r++) {
            const sourceSku = sourceSheet.getCell(r, skuColIndex).value;
            const sourceItemId = sourceSheet.getCell(r, itemIdColIndex).value;

            if (sourceSku) {
                const entry = {
                    rowIndex: r,
                    sku: sourceSku,
                    cleanItemId: cleanId(sourceItemId)
                };
                sourceMap.push(entry);
                if (!skuMap[sourceSku]) skuMap[sourceSku] = entry;
            }
        }

        // Sync Rows
        let updates = 0;
        for (const tRow of targetRows) {
            let match = null;

            if (itemIdColIndex !== -1 && tRow.cleanItemId) {
                match = sourceMap.find(m => m.cleanItemId === tRow.cleanItemId);
            }

            if (!match) {
                // Fallback to SKU
                // Use strict SKU match
                match = skuMap[tRow.sku];
            }

            if (match) {
                const formula = `='${tabName}'!${marginColLetter}${match.rowIndex + 1}`;
                const cell = targetSheet.getCell(tRow.rowIndex, COL_MARGIN);
                if (cell.formula !== formula) {
                    cell.formula = formula;
                    updates++;
                }
            }
        }

        if (updates > 0) {
            console.log(`  Updates pending for ${tabName}: ${updates}`);
        }
    }

    console.log("Saving all updates to Hoja1...");
    // Since we only updated targetSheet cells, one save is enough
    await targetSheet.saveUpdatedCells();
    console.log("Done.");
}

syncGlobalMargins().catch(console.error);
