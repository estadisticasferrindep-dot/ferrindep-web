require('dotenv').config();
const { GoogleSpreadsheet } = require('google-spreadsheet');
const { JWT } = require('google-auth-library');
const creds = require('./credentials.json');

const TARGET_MARGIN = 0.25; // 25%
// User wants "apenas un poco mas" (just a bit more), not less.
// So we treat 0.25 as a floor.
const TARGET_FLOOR = 0.2500;

const TARGET_TAB = '15x15mm';
const START_ROW = 224;
const END_ROW = 242;

const COL_C = 2;    // Cost (Index 2) - Needed to check validity
const COL_N = 13;   // Column N (Index 13)
const COL_T = 19;   // Column T (Index 19)

async function goalSeekBatch() {
    const serviceAccountAuth = new JWT({
        email: creds.client_email,
        key: creds.private_key,
        scopes: ['https://www.googleapis.com/auth/spreadsheets'],
    });

    const sheetID = process.env.SHEET_ID_PRICING || process.env.SHEET_ID;
    const doc = new GoogleSpreadsheet(sheetID, serviceAccountAuth);

    try {
        await doc.loadInfo();
        const sheet = doc.sheetsByTitle[TARGET_TAB];
        if (!sheet) { console.error("Tab not found"); return; }

        console.log(`Processing 50x50mm: Rows ${START_ROW} to ${END_ROW}`);
        console.log(`Target Margin >= ${TARGET_MARGIN * 100}%`);

        // Iterate through rows (0-indexed: Start-1 to End-1)
        for (let r = START_ROW - 1; r < END_ROW; r++) {
            await processRow(sheet, r);
        }

        console.log("------------------------------------------------");
        console.log("✅ Batch Complete.");

    } catch (e) {
        console.error("Critical Error", e);
    }
}

async function processRow(sheet, rowIndex) {
    const MAX_ITER = 15;
    let prevN = null;
    let prevT = null;

    console.log(`--- Processing Row ${rowIndex + 1} ---`);

    for (let i = 0; i < MAX_ITER; i++) {
        // Load just this row's cells
        await sheet.loadCells({
            startRowIndex: rowIndex, endRowIndex: rowIndex + 1,
            startColumnIndex: 0, endColumnIndex: 20
        });

        const cellC = sheet.getCell(rowIndex, COL_C);
        const cellN = sheet.getCell(rowIndex, COL_N);
        const cellT = sheet.getCell(rowIndex, COL_T);

        // Validation: If Cost is empty/zero, skip row
        const C = cellC.value;
        if (!C || typeof C !== 'number') {
            console.log(`   Skipping Row ${rowIndex + 1} (Empty/Invalid Cost)`);
            return;
        }

        const currentN = cellN.value;
        const currentT = cellT.value; // Margin

        // SKIP EMPTY MARGIN ROWS (Unpublished)
        if (currentT === null || currentT === '' || currentT === undefined) {
            console.log(`   Skipping Row ${rowIndex + 1} (Empty Margin/Unpublished)`);
            return;
        }

        // Check success condition: T >= 0.25 AND T < 0.251 (Close enough but over)
        // Or simply abs error if we accept slightly under.
        // User said: "puede quedar apenas un poco mas, pero no de menos".

        const diff = currentT - TARGET_FLOOR;

        // Converged if: 
        // 1. Margin is >= 25% 
        // 2. And Margin is close to 25% (within 0.1% to avoid overshooting too much)
        if (currentT >= TARGET_FLOOR && currentT < TARGET_FLOOR + 0.001) {
            console.log(`   ✅ Converged: N=${(currentN * 100).toFixed(2)}% -> T=${(currentT * 100).toFixed(2)}%`);
            return;
        }

        // Also stop if we are extremely close (floating point tolerance) e.g. 24.999%
        if (Math.abs(currentT - TARGET_FLOOR) < 0.0001) {
            console.log(`   ✅ Converged (Exact): T=${(currentT * 100).toFixed(2)}%`);
            return;
        }

        console.log(`   Iter ${i + 1}: N=${(currentN * 100).toFixed(2)}% -> T=${(currentT * 100).toFixed(2)}%`);

        let newN;

        if (prevN === null) {
            // Initial guess:
            // If T < 0.25, we need to INCREASE Price => INCREASE N.
            // Assumption: Higher N = Higher Price.
            // Let's create a small delta based on error.
            // If error is -2.5% (22.5 vs 25), try adding 5% to N.
            const error = TARGET_FLOOR - currentT;
            newN = currentN + (error * 2.5); // Heuristic multiplier
        } else {
            // Secant Method
            const dN = currentN - prevN;
            const dT = currentT - prevT;

            if (Math.abs(dT) < 0.000001) {
                newN = currentN + 0.05; // Kick
            } else {
                const slope = dT / dN;
                const gap = TARGET_FLOOR - currentT;
                // Add a tiny buffer to gap to ensure we land slightly ABOVE 0.25
                const targetWithBuffer = TARGET_FLOOR + 0.0002; // Target 25.02%
                const gapBuffer = targetWithBuffer - currentT;

                newN = currentN + (gapBuffer / slope);
            }
        }

        // Clamp
        if (isNaN(newN) || !isFinite(newN)) {
            console.error(`   ⚠️ Calculated N is invalid (${newN}). Aborting Row.`);
            return;
        }

        if (newN < 0) newN = 0.01;
        if (newN > 10) newN = 10;

        console.log(`   Adjusting N to ${newN}`); // Log value for debug

        prevN = currentN;
        prevT = currentT;

        cellN.value = newN;
        await sheet.saveUpdatedCells();

        // Short delay for Sheets recalc
        await new Promise(r => setTimeout(r, 800));
    }
    console.log(`   ⚠️ Max Iterations reached for Row ${rowIndex + 1}`);
}

goalSeekBatch();
