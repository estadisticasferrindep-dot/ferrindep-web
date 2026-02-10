require('dotenv').config();
const { GoogleSpreadsheet } = require('google-spreadsheet');
const { JWT } = require('google-auth-library');
const creds = require('./credentials.json');

async function inspectRow20Formulas() {
    const serviceAccountAuth = new JWT({
        email: creds.client_email,
        key: creds.private_key,
        scopes: ['https://www.googleapis.com/auth/spreadsheets'],
    });

    const doc = new GoogleSpreadsheet(process.env.SHEET_ID, serviceAccountAuth);
    await doc.loadInfo();
    const sheet = doc.sheetsByTitle['15x15mm'];

    await sheet.loadCells('F20:K20');

    const r = 19; // Row 20

    const cols = ['F', 'G', 'H', 'I', 'J', 'K'];
    // Indices: F=5, G=6, H=7, I=8, J=9, K=10

    console.log("--- ROW 20 FORMULAS ---");
    cols.forEach((c, idx) => {
        const cell = sheet.getCell(r, 5 + idx);
        console.log(`${c}20: Val=${cell.value} | Form=${cell.formula}`);
    });
}

inspectRow20Formulas();
