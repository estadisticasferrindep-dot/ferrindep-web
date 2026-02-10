const { GoogleSpreadsheet } = require('google-spreadsheet');
const { JWT } = require('google-auth-library');
const creds = require('./credentials.json');
require('dotenv').config();

async function readInboxRow() {
    // Stock Nuevo Sheet ID explicitly provided by user
    const SHEET_ID = '1pBIByoZ1i6pUNSn7uw2nYMTxMNWQ7bka-btA85AHYY8';

    const serviceAccountAuth = new JWT({
        email: creds.client_email,
        key: creds.private_key,
        scopes: ['https://www.googleapis.com/auth/spreadsheets'],
    });

    const doc = new GoogleSpreadsheet(SHEET_ID, serviceAccountAuth);
    await doc.loadInfo();

    const sheet = doc.sheetsByTitle['ML_Pedidos_Inbox'];
    if (!sheet) {
        console.error("Tab 'ML_Pedidos_Inbox' not found!");
        return;
    }

    // Load a chunk around row 7 (index 6)
    // Loading rows 1-10 to see header and content
    await sheet.loadCells('A1:Z10');

    console.log("--- Header (Row 1) ---");
    let headerStr = "";
    for (let c = 0; c < 20; c++) headerStr += `[${sheet.getCell(0, c).value}] `;
    console.log(headerStr);

    console.log("\n--- Row 7 (Target) ---");
    let rowStr = "";
    for (let c = 0; c < 20; c++) {
        let val = sheet.getCell(6, c).value; // Row 7 is index 6
        rowStr += `[${val}] `;
    }
    console.log(rowStr);
}

readInboxRow();
