require('dotenv').config();
const { GoogleSpreadsheet } = require('google-spreadsheet');
const { JWT } = require('google-auth-library');
const creds = require('./credentials.json');

const TARGET_TAB_NAME = 'Hoja1';
const COL_F_INDEX = 5; // Column F (0-based)
const COL_H_INDEX = 7; // SKU

async function analyzeColumnF() {
    const jwt = new JWT({
        email: creds.client_email,
        key: creds.private_key,
        scopes: ['https://www.googleapis.com/auth/spreadsheets'],
    });

    const doc = new GoogleSpreadsheet(process.env.SHEET_ID, jwt);
    await doc.loadInfo();

    const sheet = doc.sheetsByTitle[TARGET_TAB_NAME];
    if (!sheet) {
        console.log('Sheet not found');
        return;
    }

    console.log(`Loading first 100 rows of ${TARGET_TAB_NAME}...`);
    await sheet.loadCells({
        startRowIndex: 0,
        endRowIndex: 100,
        startColumnIndex: 0,
        endColumnIndex: 8 // Load up to H
    });

    const samples = [];
    for (let i = 1; i < 100; i++) { // Skip header row 0 assume
        const sku = sheet.getCell(i, COL_H_INDEX).value;
        const desc = sheet.getCell(i, COL_F_INDEX).value;

        if (sku) {
            samples.push({ row: i + 1, sku, desc });
        }
    }

    console.log('Sample Column F values:');
    samples.forEach(s => console.log(`Row ${s.row} [SKU ${s.sku}]: "${s.desc}"`));
}

analyzeColumnF().catch(console.error);
