require('dotenv').config();
const { GoogleSpreadsheet } = require('google-spreadsheet');
const { JWT } = require('google-auth-library');
const creds = require('./credentials.json');

const ROWS = [80, 83, 87, 89, 93, 96, 105, 106]; // 1-based from User
// Google Spreadsheet is 0-based. So we need row-1.

async function debugRows() {
    const auth = new JWT({
        email: creds.client_email,
        key: creds.private_key,
        scopes: ['https://www.googleapis.com/auth/spreadsheets'],
    });

    const doc = new GoogleSpreadsheet(process.env.SHEET_ID_STOCK, auth);
    await doc.loadInfo();
    const sheet = doc.sheetsByTitle['10x10mm'];

    await sheet.loadCells('A1:T200'); // Load enough to cover rows

    console.log("--- DEBUG ROWS 10x10mm ---");
    console.log("Row | Desc (F) | Cost (C/K) | Price (N) | Margin (T)");

    for (const r of ROWS) {
        const rowIndex = r - 1;
        const cellDesc = sheet.getCell(rowIndex, 5); // F is index 5
        const cellCost = sheet.getCell(rowIndex, 10); // K is index 10 (or C=2?) Standard is K Usually
        const cellPrice = sheet.getCell(rowIndex, 13); // N is index 13
        const cellMargin = sheet.getCell(rowIndex, 19); // T is index 19 (Check based on node_batch_solver.md)
        // In node_batch_solver.md: COL_C=2, COL_N=13, COL_T=19.

        console.log(`R${r} | ${cellDesc.value} | Cost:${cellCost.value} | Price:${cellPrice.value} | Margin:${cellMargin.value}`);
    }
}

debugRows();
