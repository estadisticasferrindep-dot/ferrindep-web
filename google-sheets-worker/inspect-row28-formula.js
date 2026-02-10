require('dotenv').config();
const { GoogleSpreadsheet } = require('google-spreadsheet');
const { JWT } = require('google-auth-library');
const creds = require('./credentials.json');

async function inspectRow28Formula() {
    const serviceAccountAuth = new JWT({
        email: creds.client_email,
        key: creds.private_key,
        scopes: ['https://www.googleapis.com/auth/spreadsheets'],
    });

    const doc = new GoogleSpreadsheet(process.env.SHEET_ID_STOCK, serviceAccountAuth);
    await doc.loadInfo();
    const sheet = doc.sheetsByTitle['PEDIDOS'];

    await sheet.loadCells('A28:M28');

    const r = 27; // Row 28
    const colK = sheet.getCell(r, 10); // K

    console.log("--- ROW 28 FORMULA ---");
    console.log(`Col K Value: ${colK.value}`);
    console.log(`Col K Formula: ${colK.formula}`);
}

inspectRow28Formula();
