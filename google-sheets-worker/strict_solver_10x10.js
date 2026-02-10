require('dotenv').config();
const { GoogleSpreadsheet } = require('google-spreadsheet');
const { JWT } = require('google-auth-library');
const creds = require('./credentials.json');

// --- CONFIGURATION ---
const SHEET_TITLE = '10x10mm';
const TARGET_FLOOR = 0.2500;
const TARGET_AIM = 0.2505; // Aim slightly higher to never be below 25%
const ROWS_TO_PROCESS = [80, 83, 87, 89, 93, 96, 105, 106];

// Column Indices (0-based)
const COL_N = 13; // Variable (Input Multiplier/Factor)
const COL_T = 19; // Margin (Target)

async function solveStrict() {
    const auth = new JWT({
        email: creds.client_email,
        key: creds.private_key,
        scopes: ['https://www.googleapis.com/auth/spreadsheets'],
    });

    // Use correct SHEET_ID (as per previous context which was SHEET_ID in .env, pointing to '4-1')
    const doc = new GoogleSpreadsheet(process.env.SHEET_ID, auth);
    await doc.loadInfo();
    const sheet = doc.sheetsByTitle[SHEET_TITLE]; // "10x10mm"

    console.log(`Loaded Sheet: ${doc.title} > ${sheet.title}`);
    console.log(`Target: Margin >= ${TARGET_FLOOR * 100}% (Aiming for ${TARGET_AIM * 100}%)`);
    console.log(`Modifying ONLY Column N (Index ${COL_N}).`);

    for (const r of ROWS_TO_PROCESS) {
        const rIdx = r - 1;
        await solveRow(sheet, rIdx, r);
    }
}

async function solveRow(sheet, rowIndex, rowNum) {
    console.log(`\n--- Processing R${rowNum} ---`);

    const MAX_ITER = 15;
    let prevN = null; let prevT = null;

    for (let i = 0; i < MAX_ITER; i++) {
        // Load fresh values
        await sheet.loadCells({
            startRowIndex: rowIndex, endRowIndex: rowIndex + 1,
            startColumnIndex: 0, endColumnIndex: 20
        });

        const cellN = sheet.getCell(rowIndex, COL_N);
        const cellT = sheet.getCell(rowIndex, COL_T);

        let currentN = typeof cellN.value === 'number' ? cellN.value : 0.5;
        let currentT = typeof cellT.value === 'number' ? cellT.value : 0;

        console.log(`   Iter ${i + 1}: N=${currentN.toFixed(4)}, T=${(currentT * 100).toFixed(2)}%`);

        // Check success condition: >= 25% AND close enough to not be wasteful?
        // Actually user said "25% o apenas un poco mas". 
        // So if 25.00% <= T <= 25.10%, we are good.
        if (currentT >= TARGET_FLOOR && currentT <= (TARGET_FLOOR + 0.002)) {
            console.log(`   ✅ Valid Margin! Keeping N=${currentN}`);
            break;
        }

        let newN;
        if (prevN === null) {
            // Initial nudge
            // If T < Target, we need Higher Price -> Higher N
            // Use small step
            newN = currentN + (TARGET_AIM - currentT) * 0.5;
        } else {
            const slope = (currentT - prevT) / (currentN - prevN);

            if (Math.abs(slope) < 1e-9) {
                newN = currentN + 0.05;
            } else {
                // Target 25.05%
                const rawStep = (TARGET_AIM - currentT) / slope;
                // Clamp step
                const maxStep = 0.2;
                const clampedStep = Math.max(Math.min(rawStep, maxStep), -maxStep);
                newN = currentN + clampedStep;
            }
        }

        // Anti-oscillation / Random noise if stuck
        if (newN === currentN || newN === prevN) {
            newN += 0.001 * (Math.random() > 0.5 ? 1 : -1);
        }

        // Safety clamps
        if (newN < -0.9) newN = -0.5;

        prevN = currentN; prevT = currentT;

        cellN.value = Number(newN.toFixed(4));
        await sheet.saveUpdatedCells(); // Only saving N changes
        await new Promise(r => setTimeout(r, 1500)); // Wait for recalc
    }
}

solveStrict();
