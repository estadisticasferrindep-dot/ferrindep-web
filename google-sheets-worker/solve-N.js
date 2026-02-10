require('dotenv').config();
const { GoogleSpreadsheet } = require('google-spreadsheet');
const { JWT } = require('google-auth-library');
const creds = require('./credentials.json');

// Constants derived from previous steps
const H3 = 0.865;
const I3 = 0.985;
const K3 = 0.954;
const ExtD1 = 1115;
const DenomFormula = H3 + I3 - 1; // 0.85 (Matches Z4+Z6-1)

// Derived Linear coefficients for T = (dL + y - C) / D
// L = D * Slope + Intercept
// Slope = K3 * (H3 + I3 - 1)
const Slope = K3 * DenomFormula; // 0.8109
const Intercept = -ExtD1 * K3;   // -1063.71

const TARGET_T = 0.30;

async function solve() {
    const serviceAccountAuth = new JWT({
        email: creds.client_email,
        key: creds.private_key,
        scopes: ['https://www.googleapis.com/auth/spreadsheets'],
    });

    const doc = new GoogleSpreadsheet(process.env.SHEET_ID, serviceAccountAuth);
    await doc.loadInfo();
    const sheet = doc.sheetsByTitle['15x15mm'];

    // Load C and N cols for rows 12-32 (Indices 11-31)
    // Loading Range: Rows 11 to 32 (indices 11..31 is 21 rows)
    await sheet.loadCells('C12:N32');

    console.log(`Processing rows 12-32 for Target T=${TARGET_T * 100}%...`);

    let updates = 0;

    for (let i = 11; i <= 31; i++) { // Row 12 is index 11. Row 32 is index 31.
        const cellC = sheet.getCell(i, 2);  // Col C (index 2)
        const cellN = sheet.getCell(i, 13); // Col N (index 13)

        const C = cellC.value;

        if (typeof C !== 'number' || C <= 0) {
            console.warn(`Row ${i + 1}: Invalid C value (${C}), skipping.`);
            continue;
        }

        // 1. Calculate Target D
        // T = Slope + (Intercept - C) / D
        // D = (Intercept - C) / (T - Slope)
        // D = (Intercept - C) / (0.30 - 0.8109)
        // D = (Intercept - C) / -0.5109
        const D_target = (Intercept - C) / (TARGET_T - Slope);

        // 2. Calculate Target N
        // N = [ K3 * (0.85 * D - ExtD1) / C ] - 1
        const term1 = DenomFormula * D_target - ExtD1; // (0.85 D - 1115)
        const N_target = (K3 * term1 / C) - 1;

        // Apply
        cellN.value = N_target;

        // Formatting
        cellN.numberFormat = { type: 'PERCENT', pattern: '0.00%' };
        cellN.textFormat = { fontSize: 11, foregroundColor: { red: 0, green: 0, blue: 0 } };

        console.log(`Row ${i + 1}: C=${C} -> D_tgt=${Math.round(D_target)} -> N_new=${(N_target * 100).toFixed(2)}%`);
        updates++;
    }

    if (updates > 0) {
        console.log(`Saving ${updates} updates...`);
        await sheet.saveUpdatedCells();
        console.log("✅ Done.");
    }
}

solve();
