require('dotenv').config();
const { GoogleSpreadsheet } = require('google-spreadsheet');
const { JWT } = require('google-auth-library');
const creds = require('./credentials.json');

// --- SOLVER CONSTANTS ---
const H3 = 0.865;
const I3 = 0.985;
const K3 = 0.954;
const ExtD1 = 1115;
const DenomFormula = H3 + I3 - 1;
const Slope = K3 * DenomFormula;
const Intercept = -ExtD1 * K3;
const TARGET_T = 0.30;

async function replicateAndSolve() {
    const serviceAccountAuth = new JWT({
        email: creds.client_email,
        key: creds.private_key,
        scopes: ['https://www.googleapis.com/auth/spreadsheets'],
    });

    const doc = new GoogleSpreadsheet(process.env.SHEET_ID, serviceAccountAuth);
    await doc.loadInfo();
    const sheet = doc.sheetsByTitle['15x15mm'];

    // Load Source Row 12 (Index 11) + Target Range
    await sheet.loadCells('A12:T32');

    const srcRow = 11;
    // Columns to replicate formulas from
    // 3=D, 5=F, 6=G, 9=J, 10=K, 11=L, 14=O, 19=T
    const colsToRep = [3, 5, 6, 9, 10, 11, 14, 19];

    // Get source formulas
    const sourceFormulas = {};
    colsToRep.forEach(c => {
        sourceFormulas[c] = sheet.getCell(srcRow, c).formula;
    });

    console.log("Replicating formulas and Solving for T=30%...");
    let updates = 0;

    for (let i = 11; i <= 31; i++) { // Rows 12 to 32
        // 1. REPLICATE FORMULAS
        const rowNum = i + 1;
        colsToRep.forEach(c => {
            const srcF = sourceFormulas[c];
            if (srcF) {
                // Simple regex to replace "12" with current row number
                // Be careful not to replace fixed refs like $H$3 (which has no 12).
                // Ref: D12, F12... 
                // We replace '12' with rowNum, but only if it's not preceded by specific chars? 
                // Actually, most formulas here are relative logic. =D12*...
                // Let's replace simple regex /12/g with rowNum.
                // Wait, what if value is 120?
                // Safer: regex for cell references? or just careful replace.
                // Given the context (15x15mm sheet), "12" is likely only the row ref.
                // Let's try explicit replace.
                const newF = srcF.replace(/(\D)12(\D|$)/g, `$1${rowNum}$2`);

                // Also handle end of string case if needed (though usually ) or operator follows)
                // Check T12 formula: =O12/D12

                const cell = sheet.getCell(i, c);
                if (cell.formula !== newF) {
                    cell.formula = newF;
                    updates++;
                }
            }
        });

        // 2. SOLVE FOR N (Using Logic from solve-N.js)
        const cellC = sheet.getCell(i, 2); // C
        const cellN = sheet.getCell(i, 13); // N
        const C = cellC.value;

        if (typeof C === 'number' && C > 0) {
            const D_target = (Intercept - C) / (TARGET_T - Slope);
            const term1 = DenomFormula * D_target - ExtD1;
            const N_target = (K3 * term1 / C) - 1;

            cellN.value = N_target;
            cellN.numberFormat = { type: 'PERCENT', pattern: '0.00%' };
            cellN.textFormat = { fontSize: 11, foregroundColor: { red: 0, green: 0, blue: 0 } };
            updates++;
        }
    }

    console.log(`Saving ${updates} changes...`);
    await sheet.saveUpdatedCells();
    console.log("✅ Done.");
}

replicateAndSolve();
