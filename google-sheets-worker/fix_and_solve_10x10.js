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
const COL_D = 3;  // Final Price (Should be Formula)
const COL_N = 13; // Variable (Input Multiplier/Factor)
const COL_T = 19; // Margin (Target)
// Formulas use A1 notation, so C=2, I=8, N=13
// Formula Pattern: =ROUND( ((C82*(1+N82)/$AD$8) - $I82) / ($AD$4 + $AD$6 - 1); 0 )

async function solveBatch() {
    const auth = new JWT({
        email: creds.client_email,
        key: creds.private_key,
        scopes: ['https://www.googleapis.com/auth/spreadsheets'],
    });

    const doc = new GoogleSpreadsheet(process.env.SHEET_ID, auth);
    await doc.loadInfo();
    const sheet = doc.sheetsByTitle[SHEET_TITLE];

    console.log(`Loaded Sheet: ${doc.title} > ${sheet.title}`);

    for (const r of ROWS_TO_PROCESS) {
        const rIdx = r - 1;
        await restoreFormulaAndSolve(sheet, rIdx, r);
    }
}

async function restoreFormulaAndSolve(sheet, rowIndex, rowNum) {
    console.log(`\n--- Processing R${rowNum} ---`);

    await sheet.loadCells({
        startRowIndex: rowIndex, endRowIndex: rowIndex + 1,
        startColumnIndex: 0, endColumnIndex: 20
    });

    // 1. RESTORE FORMULA IN COLUMN D
    const cellD = sheet.getCell(rowIndex, COL_D);
    // Construct formula dynamically based on row number
    // Note: JS uses semi-colon for formulas sometimes? Google API usually expects standard comma or depending on locale.
    // However, the dump showed ";". Let's try to match exactly what we saw in R82 dump but adjusted for row.
    // REF: =ROUND( ((C82*(1+N82)/$AD$8) - $I82) / ($AD$4 + $AD$6 - 1); 0 )
    const formulaRep = `=ROUND( ((C${rowNum}*(1+N${rowNum})/$AD$8) - $I${rowNum}) / ($AD$4 + $AD$6 - 1); 0 )`;

    cellD.formula = formulaRep;
    // We save immediately to verify formula takes effect and see what value we get with current N
    await sheet.saveUpdatedCells();

    // allow calc time
    await new Promise(r => setTimeout(r, 1000));

    // Reload to get calculated values
    await sheet.loadCells({
        startRowIndex: rowIndex, endRowIndex: rowIndex + 1,
        startColumnIndex: 0, endColumnIndex: 20
    });

    // 2. SOLVE GOAL SEEK (Target T=25% by changing N)
    const MAX_ITER = 15;
    let prevN = null; let prevT = null;

    for (let i = 0; i < MAX_ITER; i++) {
        // Reload fresh values
        await sheet.loadCells({
            startRowIndex: rowIndex, endRowIndex: rowIndex + 1,
            startColumnIndex: 0, endColumnIndex: 20
        });

        const cellN = sheet.getCell(rowIndex, COL_N);
        const cellT = sheet.getCell(rowIndex, COL_T);

        let currentN = typeof cellN.value === 'number' ? cellN.value : 0.5; // Default start if empty
        let currentT = typeof cellT.value === 'number' ? cellT.value : 0;

        console.log(`   Iter ${i + 1}: N=${currentN.toFixed(4)}, T=${(currentT * 100).toFixed(2)}%`);

        if (Math.abs(currentT - TARGET_MARGIN) < 0.0005) { // 0.05% tolerance
            console.log(`   Converged!`);
            break;
        }

        let newN;
        if (prevN === null) {
            newN = currentN + (TARGET_MARGIN - currentT) * 0.5; // Reduced from 2.0 to 0.5 for gentler start
        } else {
            const slope = (currentT - prevT) / (currentN - prevN);

            if (Math.abs(slope) < 1e-9) {
                newN = currentN + 0.05;
            } else {
                // Dampened Secant: Limit step size
                const rawStep = (TARGET_MARGIN - 0.0005 - currentT) / slope;
                const maxStep = 0.2; // Limit max jump
                const clampedStep = Math.max(Math.min(rawStep, maxStep), -maxStep);

                newN = currentN + clampedStep;
            }
        }

        // Anti-oscillation noise
        if (newN === currentN || newN === prevN) {
            newN += 0.001 * (Math.random() > 0.5 ? 1 : -1);
        }

        // Safety clamps for N (Multiplier usually > -1 ?)
        // Formula: 1+N. If N=-1, Price=0. So N must be > -1. 
        if (newN < -0.9) newN = -0.5;

        prevN = currentN; prevT = currentT;

        // Rounding N input might help stability if sheet rounds it? No, keep substantial precision.
        cellN.value = Number(newN.toFixed(4));
        await sheet.saveUpdatedCells();
        await new Promise(r => setTimeout(r, 1500));
    }
}

solveBatch();
