require('dotenv').config();
const { GoogleSpreadsheet } = require('google-spreadsheet');
const { JWT } = require('google-auth-library');
const creds = require('./credentials.json');

const TARGET_TAB = '20x20mm';
const LOOK_ROWS = [45, 46];

async function checkRows() {
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
        if (!sheet) { console.error(`Tab '${TARGET_TAB}' not found`); return; }

        console.log(`Inspecting '${TARGET_TAB}' Rows ${LOOK_ROWS.join(', ')}...`);

        await sheet.loadCells('A40:Z50'); // Load enough context

        for (const rNum of LOOK_ROWS) {
            const idx = rNum - 1;
            const cellSKU = sheet.getCell(idx, 0);
            const val = cellSKU.value;
            console.log(`\nRow ${rNum}:`);
            console.log(`   SKU Value: ${val}`);
            console.log(`   SKU Type: ${typeof val}`);
            console.log(`   SKU Raw: ${JSON.stringify(val)}`);

            const cellPrice = sheet.getCell(idx, 4);
            const cellMargin = sheet.getCell(idx, 19);
            console.log(`   Price: ${cellPrice.value}`);
            console.log(`   Margin: ${cellMargin.value}`);
        }

    } catch (e) {
        console.error("Error:", e);
    }
}

checkRows();
