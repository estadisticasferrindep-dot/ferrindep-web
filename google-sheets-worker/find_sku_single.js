require('dotenv').config();
const { GoogleSpreadsheet } = require('google-spreadsheet');
const { JWT } = require('google-auth-library');
const creds = require('./credentials.json');

const TARGET_TAB = '15x15mm';
const TARGET_SKU = 1050;

async function findSKU() {
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

        console.log(`Scanning '${TARGET_TAB}' for SKU ${TARGET_SKU}...`);

        await sheet.loadCells('A1:T200'); // Load enough rows

        for (let i = 0; i < 200; i++) {
            const cellSKU = sheet.getCell(i, 0); // Col A
            if (cellSKU.value == TARGET_SKU) {
                const cellLen = sheet.getCell(i, 1); // Col B
                const cellPrice = sheet.getCell(i, 4); // Col E
                const cellMargin = sheet.getCell(i, 19); // Col T

                console.log(`\nMatch Found at Row ${i + 1}:`);
                console.log(`- Largo (Col B): ${cellLen.value} metros`);
                console.log(`- Precio (Col E): $${cellPrice.value}`);
                console.log(`- Margen (Col T): ${(cellMargin.value * 100).toFixed(2)}%`);
                return;
            }
        }
        console.log("SKU not found in first 200 rows.");

    } catch (e) {
        console.error("Error:", e);
    }
}

findSKU();
