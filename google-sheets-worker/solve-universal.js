require('dotenv').config();
const { GoogleSpreadsheet } = require('google-spreadsheet');
const { JWT } = require('google-auth-library');
const creds = require('./credentials.json');

// --- CONSTANTS ---
const H3 = 0.865;
const I3 = 0.985;
const K3 = 0.954;
const ExtD1 = 1115;
const Denom = H3 + I3 - 1; // 0.85
const Slope = K3 * Denom;  // 0.8109
const TARGET_T = 0.30;

async function solveUniversal() {
    const serviceAccountAuth = new JWT({
        email: creds.client_email,
        key: creds.private_key,
        scopes: ['https://www.googleapis.com/auth/spreadsheets'],
    });

    const doc = new GoogleSpreadsheet(process.env.SHEET_ID, serviceAccountAuth);
    await doc.loadInfo();
    const sheet = doc.sheetsByTitle['15x15mm'];

    // Load C (2), H (7), I (8), N (13)
    // Range 12-32 -> Indices 11-31
    // Load a bounding box covering all
    await sheet.loadCells('C12:N32');

    console.log("Running Universal Solver (accounting for H and I columns)...");
    let updates = 0;

    for (let i = 11; i <= 31; i++) {
        const cellC = sheet.getCell(i, 2);
        const cellH = sheet.getCell(i, 7);
        const cellI = sheet.getCell(i, 8);
        const cellN = sheet.getCell(i, 13);

        const C = cellC.value;
        const H = (typeof cellH.value === 'number') ? cellH.value : 0;
        const I = (typeof cellI.value === 'number') ? cellI.value : 0;

        if (typeof C === 'number' && C > 0) {
            // 1. Calculate Intercept specific to this row
            // Intercept = K3 * (H + I - ExtD1)
            const Intercept_Row = K3 * (H + I - ExtD1);

            // 2. Calculate D Target
            // D = (Intercept - C) / (TargetT - Slope)
            const D_target = (Intercept_Row - C) / (TARGET_T - Slope);

            // 3. Calculate N Target
            // N = [ K3 * (Denom * D + I - ExtD1) / C ] - 1
            const numerator = K3 * (Denom * D_target + I - ExtD1);
            const N_target = (numerator / C) - 1;

            // Update
            // Only check difference if significant? N values are precise.
            // Let's just overwrite to be safe.
            cellN.value = N_target;
            cellN.numberFormat = { type: 'PERCENT', pattern: '0.00%' };
            cellN.textFormat = { fontSize: 11, foregroundColor: { red: 0, green: 0, blue: 0 } };

            updates++;
            console.log(`Row ${i + 1}: C=${C}, H=${H}, I=${I} -> N=${(N_target * 100).toFixed(2)}%`);
        } else {
            // Skip empty rows (shouldn't be any in this range ideally, but consistent with clean script)
            // console.log(`Row ${i+1}: Skipped (C is empty/invalid)`);
        }
    }

    console.log(`Saving ${updates} updates...`);
    await sheet.saveUpdatedCells();
    console.log("✅ Universal Solver Done.");
}

solveUniversal();
