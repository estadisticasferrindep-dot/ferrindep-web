require('dotenv').config();
const { GoogleSpreadsheet } = require('google-spreadsheet');
const { JWT } = require('google-auth-library');
const creds = require('./credentials.json');

async function debug1m() {
    const serviceAccountAuth = new JWT({
        email: creds.client_email,
        key: creds.private_key,
        scopes: ['https://www.googleapis.com/auth/spreadsheets'],
    });

    const doc = new GoogleSpreadsheet(process.env.SHEET_ID, serviceAccountAuth);
    await doc.loadInfo();
    const sheet = doc.sheetsByTitle['10x10mm'];

    await sheet.loadCells('A72:K108');

    console.log("--- DEBUG 1m Section (Rows 72-108) ---");
    for (let r = 72; r < 108; r++) {
        const meters = sheet.getCell(r, 1).value;
        if (meters === 1) {
            const price = sheet.getCell(r, 3).value;
            const desc = sheet.getCell(r, 0).value;
            console.log(`Row ${r + 1}: 1m - Score/Desc: ${desc} - Price: $${price}`);
        }
    }
}

debug1m();
