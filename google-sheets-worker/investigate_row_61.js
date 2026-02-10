const { GoogleSpreadsheet } = require('google-spreadsheet');
const { JWT } = require('google-auth-library');
require('dotenv').config();
const creds = require('./credentials.json');

async function inspectRow() {
    const serviceAccountAuth = new JWT({
        email: creds.client_email,
        key: creds.private_key,
        scopes: ['https://www.googleapis.com/auth/spreadsheets'],
    });

    const doc = new GoogleSpreadsheet(process.env.SHEET_ID, serviceAccountAuth);
    await doc.loadInfo();
    const sheet = doc.sheetsByTitle['20x20mm'];

    // Row 61 (Index 60)
    const ROW_IDX = 60;

    await sheet.loadCells(`A${ROW_IDX + 1}:T${ROW_IDX + 1}`);

    const sku = sheet.getCell(ROW_IDX, 0).value;
    const cost = sheet.getCell(ROW_IDX, 2).formattedValue; // Col C
    const price = sheet.getCell(ROW_IDX, 6).formattedValue; // Col G (Guessing Price location, will check multiple)
    const mlFee = sheet.getCell(ROW_IDX, 10).formattedValue; // Col K?
    const shipping = sheet.getCell(ROW_IDX, 11).formattedValue; // Col L?
    const margin = sheet.getCell(ROW_IDX, 19).formattedValue; // Col T (User confirmed)

    // Let's dump the whole row values to be sure where things are
    console.log(`--- Row ${ROW_IDX + 1} Data ---`);
    for (let c = 0; c < 20; c++) {
        const cell = sheet.getCell(ROW_IDX, c);
        console.log(`Col ${String.fromCharCode(65 + c)}: ${cell.formattedValue} (Raw: ${cell.value})`);
    }
}

inspectRow();
