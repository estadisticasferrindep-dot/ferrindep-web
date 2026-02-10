require('dotenv').config();
const { GoogleSpreadsheet } = require('google-spreadsheet');
const { JWT } = require('google-auth-library');
const creds = require('./credentials.json');

async function fetchConstants() {
    const serviceAccountAuth = new JWT({
        email: creds.client_email,
        key: creds.private_key,
        scopes: ['https://www.googleapis.com/auth/spreadsheets'],
    });

    const doc = new GoogleSpreadsheet(process.env.SHEET_ID, serviceAccountAuth);
    await doc.loadInfo();

    const sheet15 = doc.sheetsByTitle['15x15mm'];
    const sheet10 = doc.sheetsByTitle['10x10mm'];

    if (!sheet15 || !sheet10) {
        console.error("Sheets not found"); process.exit(1);
    }

    // Load Cells
    await sheet15.loadCells(['H3', 'I3', 'K3', 'Z4', 'Z6', 'Z8', 'Z10']);
    await sheet10.loadCells(['D1']);

    const H3 = sheet15.getCellByA1('H3').value;
    const I3 = sheet15.getCellByA1('I3').value;
    const K3 = sheet15.getCellByA1('K3').value;

    const Z4 = sheet15.getCellByA1('Z4').value;
    const Z6 = sheet15.getCellByA1('Z6').value;
    const Z8 = sheet15.getCellByA1('Z8').value;
    const Z10 = sheet15.getCellByA1('Z10').value;

    const ExtD1 = sheet10.getCellByA1('D1').value;

    console.log("--- CONSTANTS ---");
    console.log("H3:", H3);
    console.log("I3:", I3);
    console.log("K3:", K3);
    console.log("Z4:", Z4);
    console.log("Z6:", Z6);
    console.log("Z8:", Z8);
    console.log("Z10:", Z10);
    console.log("ExtD1 ('10x10mm'!D1):", ExtD1);
}

fetchConstants();
