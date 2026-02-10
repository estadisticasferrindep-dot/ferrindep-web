require('dotenv').config();
const { GoogleSpreadsheet } = require('google-spreadsheet');
const { JWT } = require('google-auth-library');
const creds = require('./credentials.json');

async function inspectRow20() {
    const serviceAccountAuth = new JWT({
        email: creds.client_email,
        key: creds.private_key,
        scopes: ['https://www.googleapis.com/auth/spreadsheets'],
    });

    const doc = new GoogleSpreadsheet(process.env.SHEET_ID, serviceAccountAuth);
    await doc.loadInfo();
    const sheet = doc.sheetsByTitle['15x15mm'];

    await sheet.loadCells('C20:T20');

    const r = 19; // Index for Row 20

    const cellC = sheet.getCell(r, 2);
    const cellN = sheet.getCell(r, 13);
    const cellD = sheet.getCell(r, 3);
    const cellO = sheet.getCell(r, 14);
    const cellT = sheet.getCell(r, 19);

    console.log("--- ROW 20 ANALYSIS ---");
    console.log(`C20: ${cellC.value}`);
    console.log(`N20: ${cellN.value}`);
    console.log(`D20: Val=${cellD.value} | Form=${cellD.formula}`);
    console.log(`O20: Val=${cellO.value} | Form=${cellO.formula}`);
    console.log(`T20: Val=${cellT.value} | Form=${cellT.formula}`);
}

inspectRow20();
