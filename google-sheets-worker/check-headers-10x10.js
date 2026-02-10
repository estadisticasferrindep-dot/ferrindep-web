require('dotenv').config();
const { GoogleSpreadsheet } = require('google-spreadsheet');
const { JWT } = require('google-auth-library');
const creds = require('./credentials.json');

async function check10x10Headers() {
    const serviceAccountAuth = new JWT({
        email: creds.client_email,
        key: creds.private_key,
        scopes: ['https://www.googleapis.com/auth/spreadsheets'],
    });

    const doc = new GoogleSpreadsheet(process.env.SHEET_ID_STOCK, serviceAccountAuth);
    await doc.loadInfo();

    const sheet = doc.sheetsByTitle['10x10mm'];
    await sheet.loadCells('A1:K5');

    console.log("--- HEADERS (10x10) ---");
    for (let r = 0; r < 5; r++) {
        const rowVals = [];
        for (let c = 0; c < 11; c++) {
            const val = sheet.getCell(r, c).value;
            if (val) rowVals.push(`[${c}] ${val}`);
        }
        if (rowVals.length) console.log(`R${r + 1}: ${rowVals.join(', ')}`);
    }
}

check10x10Headers();
