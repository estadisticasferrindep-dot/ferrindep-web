require('dotenv').config();
const { GoogleSpreadsheet } = require('google-spreadsheet');
const { JWT } = require('google-auth-library');
const creds = require('./credentials.json');

const TOKEN_TAB_NAME = 'Tokens';

async function inspectTokens() {
    const jwt = new JWT({
        email: creds.client_email,
        key: creds.private_key,
        scopes: ['https://www.googleapis.com/auth/spreadsheets'],
    });

    const doc = new GoogleSpreadsheet(process.env.SHEET_ID, jwt);
    await doc.loadInfo();

    const sheet = doc.sheetsByTitle[TOKEN_TAB_NAME];
    if (!sheet) {
        console.error(`Tab '${TOKEN_TAB_NAME}' not found.`);
        return;
    }

    console.log(`Loading first 10 rows of '${TOKEN_TAB_NAME}'...`);
    await sheet.loadCells('A1:E10'); // Load A to E, first 10 rows

    for (let i = 0; i < 10; i++) {
        const rowData = [];
        for (let j = 0; j < 5; j++) { // Cols A-E
            const val = sheet.getCell(i, j).value;
            rowData.push(val);
        }
        console.log(`Row ${i + 1}:`, rowData);
    }
}

inspectTokens().catch(console.error);
