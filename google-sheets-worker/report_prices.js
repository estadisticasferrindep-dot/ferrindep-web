require('dotenv').config();
const { GoogleSpreadsheet } = require('google-spreadsheet');
const { JWT } = require('google-auth-library');
const creds = require('./credentials.json');

// Configuration
const START_ROW = 113;
const END_ROW = 131; // Inclusive

async function reportPrices() {
    const serviceAccountAuth = new JWT({
        email: creds.client_email,
        key: creds.private_key,
        scopes: ['https://www.googleapis.com/auth/spreadsheets'],
    });

    const sheetID = process.env.SHEET_ID_PRICING || process.env.SHEET_ID;
    const doc = new GoogleSpreadsheet(sheetID, serviceAccountAuth);

    try {
        await doc.loadInfo();
        const sheet = doc.sheetsByTitle['50x50mm'];
        if (!sheet) { console.error("Tab '50x50mm' not found"); return; }

        console.log(`--- Price Report: Row ${START_ROW} to ${END_ROW} ---`);
        console.log(`Row | Length (Col B) | Price/m (Col E) | Margin (Col T)`);
        console.log(`----|----------------|-----------------|---------------`);

        // Load range
        await sheet.loadCells({
            startRowIndex: START_ROW - 1, endRowIndex: END_ROW,
            startColumnIndex: 0, endColumnIndex: 20
        });

        for (let r = START_ROW; r <= END_ROW; r++) {
            const rx = r - 1;
            const cellLength = sheet.getCell(rx, 1); // Col B (Index 1)
            const cellPriceM = sheet.getCell(rx, 4); // Col E (Index 4)
            const cellMargin = sheet.getCell(rx, 19); // Col T (Index 19)

            const len = cellLength.value;
            const pm = cellPriceM.value;
            const margin = cellMargin.value;

            // Simple formatting
            let pmStr = (typeof pm === 'number') ? `$${pm.toFixed(2)}` : String(pm);
            let marginStr = (typeof margin === 'number') ? `${(margin * 100).toFixed(2)}%` : String(margin);

            if (pmStr === 'null' || pmStr === 'undefined') pmStr = '-';
            if (marginStr === '0.00%' || marginStr === 'null') marginStr = '-';

            // Check for monotonicity flag
            // (We can't easily check prev row here without storing it, but let's just dump data)

            console.log(`${r}  | ${String(len).padEnd(14)} | ${pmStr.padEnd(15)} | ${marginStr}`);
        }

    } catch (e) {
        console.error("Critical Error", e);
    }
}

reportPrices();
