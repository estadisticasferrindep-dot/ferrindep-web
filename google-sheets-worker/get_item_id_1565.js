const { GoogleSpreadsheet } = require('google-spreadsheet');
const { JWT } = require('google-auth-library');
require('dotenv').config();
const creds = require('./credentials.json');

async function getItemId() {
    const serviceAccountAuth = new JWT({
        email: creds.client_email,
        key: creds.private_key,
        scopes: ['https://www.googleapis.com/auth/spreadsheets'],
    });

    const doc = new GoogleSpreadsheet(process.env.SHEET_ID, serviceAccountAuth);
    await doc.loadInfo();
    const sheet = doc.sheetsByTitle['20x20mm'];

    // Row 67 (Index 66)
    const ROW_IDX = 66;

    await sheet.loadCells(`A${ROW_IDX + 1}:T${ROW_IDX + 1}`);

    const sku = sheet.getCell(ROW_IDX, 0).value;
    const itemId = sheet.getCell(ROW_IDX, 18).value; // Col S

    console.log(`SKU: ${sku}`);
    console.log(`Item ID: ${itemId}`);
}

getItemId();
