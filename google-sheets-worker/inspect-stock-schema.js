require('dotenv').config();
const { GoogleSpreadsheet } = require('google-spreadsheet');
const { JWT } = require('google-auth-library');
const creds = require('./credentials.json');

async function inspectSchema() {
    const serviceAccountAuth = new JWT({
        email: creds.client_email,
        key: creds.private_key,
        scopes: ['https://www.googleapis.com/auth/spreadsheets'],
    });

    const doc = new GoogleSpreadsheet(process.env.SHEET_ID_STOCK, serviceAccountAuth);
    await doc.loadInfo();

    // Inspect 'PEDIDOS' tab
    const sheet = doc.sheetsByTitle['PEDIDOS'];
    if (!sheet) {
        console.log("❌ Tab 'PEDIDOS' not found.");
        return;
    }

    // Load headers (Row 1)
    await sheet.loadHeaderRow();
    const headers = sheet.headerValues;

    console.log("--- COLUMNS IN 'PEDIDOS' ---");
    headers.forEach((h, i) => {
        console.log(`[${i}] ${h}`);
    });

    // Also check 'STOCK LOCAL' columns for reference
    const sheetStock = doc.sheetsByTitle['STOCK LOCAL'];
    if (sheetStock) {
        await sheetStock.loadHeaderRow();
        console.log("\n--- COLUMNS IN 'STOCK LOCAL' ---");
        sheetStock.headerValues.forEach((h, i) => {
            console.log(`[${i}] ${h}`);
        });
    }
}

inspectSchema();
