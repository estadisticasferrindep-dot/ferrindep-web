require('dotenv').config();
const { GoogleSpreadsheet } = require('google-spreadsheet');
const { JWT } = require('google-auth-library');
const creds = require('./credentials.json');

async function inspectL() {
    const serviceAccountAuth = new JWT({
        email: creds.client_email,
        key: creds.private_key,
        scopes: ['https://www.googleapis.com/auth/spreadsheets'],
    });

    const doc = new GoogleSpreadsheet(process.env.SHEET_ID, serviceAccountAuth);
    await doc.loadInfo();

    const sheet = doc.sheetsByTitle['15x15mm'];

    await sheet.loadCells('L12');
    const cellL = sheet.getCell(11, 11); // Col L is index 11 (A=0 ... L=11)

    console.log("--- L12 ANALYSIS ---");
    console.log("L12 Value:", cellL.value);
    console.log("L12 Formula:", cellL.formula);
}

inspectL();
