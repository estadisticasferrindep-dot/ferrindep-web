require('dotenv').config();
const { GoogleSpreadsheet } = require('google-spreadsheet');
const { JWT } = require('google-auth-library');
const creds = require('./credentials.json');

async function inspectRow14() {
    const serviceAccountAuth = new JWT({
        email: creds.client_email,
        key: creds.private_key,
        scopes: ['https://www.googleapis.com/auth/spreadsheets'],
    });

    const doc = new GoogleSpreadsheet(process.env.SHEET_ID, serviceAccountAuth);
    await doc.loadInfo();
    const sheet = doc.sheetsByTitle['15x15mm'];

    await sheet.loadCells('C14:T14');
    await sheet.loadCells('F14:K14'); // Params

    const r = 13; // Index for Row 14

    console.log("--- ROW 14 ANALYSIS ---");
    console.log("C14:", sheet.getCell(r, 2).value);
    console.log("N14:", sheet.getCell(r, 13).value);
    console.log("T14:", sheet.getCell(r, 19).value);

    console.log("D14 Formula:", sheet.getCell(r, 3).formula);
    console.log("F14 Formula:", sheet.getCell(r, 5).formula);
    console.log("G14 Formula:", sheet.getCell(r, 6).formula);
    console.log("L14 Formula:", sheet.getCell(r, 11).formula);
    console.log("O14 Formula:", sheet.getCell(r, 14).formula);
}

inspectRow14();
