require('dotenv').config();
const { GoogleSpreadsheet } = require('google-spreadsheet');
const { JWT } = require('google-auth-library');
const creds = require('./credentials.json');

async function flagRow33() {
    const serviceAccountAuth = new JWT({
        email: creds.client_email,
        key: creds.private_key,
        scopes: ['https://www.googleapis.com/auth/spreadsheets'],
    });

    const doc = new GoogleSpreadsheet(process.env.SHEET_ID_STOCK, serviceAccountAuth);
    await doc.loadInfo();

    const sheet = doc.sheetsByTitle['PEDIDOS'];
    const r = 32; // Row 33

    await sheet.loadCells('A33:N33');

    const cellNote = sheet.getCell(r, 13); // Col N
    cellNote.value = "no se anotó sobrante";

    await sheet.saveUpdatedCells();
    console.log("Updated Row 33, Column N with note.");
}

flagRow33();
