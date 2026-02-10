const { GoogleSpreadsheet } = require('google-spreadsheet');
const { JWT } = require('google-auth-library');
require('dotenv').config();
const creds = require('./credentials.json');

async function searchAllTabs() {
    const serviceAccountAuth = new JWT({
        email: creds.client_email,
        key: creds.private_key,
        scopes: ['https://www.googleapis.com/auth/spreadsheets'],
    });

    const doc = new GoogleSpreadsheet(process.env.SHEET_ID, serviceAccountAuth);
    await doc.loadInfo();

    const targets = ['2945', '2950', '2905', '2895'];
    console.log(`Scanning ALL ${doc.sheetCount} tabs for SKUs: ${targets.join(', ')}...`);

    for (let i = 0; i < doc.sheetCount; i++) {
        const sheet = doc.sheetsByIndex[i];
        console.log(`> Checking '${sheet.title}'...`);
        try {
            await sheet.loadCells('A1:A300'); // Check first 300 rows
            for (let r = 0; r < 300; r++) {
                const val = sheet.getCell(r, 0).value;
                if (val && targets.includes(String(val).trim())) {
                    console.log(`[FOUND] SKU ${val} in '${sheet.title}' Row ${r + 1}`);
                    await sheet.loadCells(`S${r + 1}:S${r + 1}`); // Load ID col separately if needed? No, A1:S300 better.
                    // Just load S column for this row now.
                }
            }
        } catch (e) {
            // Ignore empty or error tabs
        }
    }
}

searchAllTabs();
