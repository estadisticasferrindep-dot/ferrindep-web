require('dotenv').config();
const { GoogleSpreadsheet } = require('google-spreadsheet');
const { JWT } = require('google-auth-library');
const creds = require('./credentials.json');

// --- CONFIGURATION ---
const SHEET_TITLE = '10x10mm';
const TARGET_MARGIN = 0.2500; // 25%
const ROWS_TO_PROCESS = [80, 83, 87, 89, 93, 96, 105, 106]; // 1-based indices from User
// Will be converted to 0-based index.

// Column Indices (0-based)
const COL_E = 4;  // Price Per Meter (Scaling Metric)
const COL_N = 3;  // Final Price (Input Variable - D) - Dump showed [3]=84433
const COL_T = 19; // Margin (Target Variable - T) - Dump showed [19]=0.29

async function solveBatch() {
    const auth = new JWT({
        email: creds.client_email,
        key: creds.private_key,
        scopes: ['https://www.googleapis.com/auth/spreadsheets'],
    });

    // Use SHEET_ID (Pricing Sheet "4-1")
    const doc = new GoogleSpreadsheet(process.env.SHEET_ID, auth);
    await doc.loadInfo();
    const sheet = doc.sheetsByTitle[SHEET_TITLE];

    console.log(`Loaded Sheet: ${doc.title} > ${sheet.title}`);

    // Sort rows for backward processing later
    // Indices are essential.
    const rowIndices = ROWS_TO_PROCESS.map(r => r - 1).sort((a, b) => a - b);

    // --- STEP 1: NORMALIZE TO 25% ---
    console.log("\n--- STEP 1: NORMALIZATION (Target 25%) ---");
    for (const rIdx of rowIndices) {
        await processGoalSeek(sheet, rIdx);
    }

    /* 
    // --- STEP 2: BACKWARD SCALING (DISABLED) ---
    console.log("\n--- STEP 2: BACKWARD SCALING (SKIPPED) ---");
    */
}

// --- SOLVER FUNCTIONS ---

async function processGoalSeek(sheet, rowIndex) {
    const MAX_ITER = 15;
    let prevN = null; let prevT = null;

    console.log(`[GoalSeek] Processing R${rowIndex + 1}...`);

    for (let i = 0; i < MAX_ITER; i++) {
        await sheet.loadCells({
            startRowIndex: rowIndex, endRowIndex: rowIndex + 1,
            startColumnIndex: 0, endColumnIndex: 20
        });

        const cellN = sheet.getCell(rowIndex, COL_N);
        const cellT = sheet.getCell(rowIndex, COL_T);

        if (!cellN.formula && typeof cellN.value !== 'number') {
            console.log(`Skipping R${rowIndex + 1} (Empty/Invalid)`);
            return;
        }

        const currentN = cellN.value;
        const currentT = cellT.value;

        // Convergence Check (Target 25%)
        // Abs Error < 0.001 (0.1%)
        if (Math.abs(currentT - TARGET_MARGIN) < 0.001) {
            console.log(`R${rowIndex + 1} Converged: Price=${currentN}, Margin=${(currentT * 100).toFixed(2)}%`);
            return;
        }

        let newN;
        if (prevN === null) {
            // Initial Guess: Adjust proportional to error? 
            // If Margin is Low (0.10 vs 0.25), we need HIGHER price.
            // Delta = 0.15. 
            // Heuristic: Price * (1 + Delta)
            newN = currentN * (1 + (TARGET_MARGIN - currentT));
        } else {
            // Secant Method
            const slope = (currentT - prevT) / (currentN - prevN);
            if (Math.abs(slope) < 1e-9) {
                newN = currentN * 1.05; // Force move if stuck
            } else {
                newN = currentN + ((TARGET_MARGIN - currentT) / slope);
            }
        }

        // Safety Clamps (avoid negative or huge jumps)
        if (newN < 0) newN = currentN * 1.1;

        prevN = currentN; prevT = currentT;

        if (isNaN(newN)) {
            console.error(`Error: newN is NaN for R${rowIndex + 1}. CurrentN=${currentN}, CurrentT=${currentT}`);
            // Fallback move
            newN = currentN * 1.05;
        }

        cellN.value = Math.round(newN); // Round to integer preferred? 

        await sheet.saveUpdatedCells();
        // Wait for recalc
        await new Promise(r => setTimeout(r, 1500));
    }
    console.log(`R${rowIndex + 1} Iteration Limit Reached.`);
}

async function solveForTargetE(sheet, rowIndex, targetE) {
    const MAX_ITER = 10;

    for (let i = 0; i < MAX_ITER; i++) {
        await sheet.loadCells({
            startRowIndex: rowIndex, endRowIndex: rowIndex + 1,
            startColumnIndex: 0, endColumnIndex: 20
        });

        const cellN = sheet.getCell(rowIndex, COL_N); // Input Price
        const cellE = sheet.getCell(rowIndex, COL_E); // Output Price/Mt

        const currentE = cellE.value;
        if (currentE >= targetE) return; // Done

        const currentN = cellN.value;

        // Heuristic: E is linear to N usually. 
        // Factor = TargetE / CurrentE
        // NewN = CurrentN * Factor

        const factor = targetE / currentE;
        // Add a tiny buffer (1.001) to ensure we cross the threshold
        let newN = currentN * factor * 1.001;

        cellN.value = Math.round(newN);
        await sheet.saveUpdatedCells();
        await new Promise(r => setTimeout(r, 1500));
    }
}

solveBatch();
