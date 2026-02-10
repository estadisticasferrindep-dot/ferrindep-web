require('dotenv').config();
const { GoogleSpreadsheet } = require('google-spreadsheet');
const { JWT } = require('google-auth-library');
const creds = require('./credentials.json');

async function inspectParams() {
    const serviceAccountAuth = new JWT({
        email: creds.client_email,
        key: creds.private_key,
        scopes: ['https://www.googleapis.com/auth/spreadsheets'],
    });

    const doc = new GoogleSpreadsheet(process.env.SHEET_ID, serviceAccountAuth);
    await doc.loadInfo();
    const sheet = doc.sheetsByTitle['15x15mm'];

    await sheet.loadCells('F12:K12');

    const cols = ['F', 'G', 'H', 'I', 'J', 'K'];
    const indices = [5, 6, 7, 8, 9, 10]; // Indexes

    console.log("--- PARAMS ANALYSIS (Row 12) ---");
    indices.forEach((idx, i) => {
        const cell = sheet.getCell(11, idx);
        console.log(`Col ${cols[i]}: Val=${cell.value} | Formula=${cell.formula}`);
    });
}

inspectParams();
