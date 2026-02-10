require('dotenv').config();
const { GoogleSpreadsheet } = require('google-spreadsheet');
const { JWT } = require('google-auth-library');
const creds = require('./credentials.json');

async function cleanRows() {
    const serviceAccountAuth = new JWT({
        email: creds.client_email,
        key: creds.private_key,
        scopes: ['https://www.googleapis.com/auth/spreadsheets'],
    });

    const doc = new GoogleSpreadsheet(process.env.SHEET_ID, serviceAccountAuth);
    await doc.loadInfo();
    const sheet = doc.sheetsByTitle['15x15mm'];

    // Range 12-32 (Index 11-31)
    await sheet.loadCells('C12:T32');

    const colsToClean = [3, 5, 6, 9, 10, 11, 14, 19]; // D, F, G, J, K, L, O, T

    let cleaned = 0;

    for (let i = 11; i <= 31; i++) {
        const cellS = sheet.getCell(i, 18); // S (ML ID)
        const valS = cellS.value;

        // Check if empty
        if (!valS || String(valS).trim() === '') {
            colsToClean.forEach(c => {
                const cell = sheet.getCell(i, c);
                cell.value = null; // Clear content (removes formula)
            });
            cleaned++;
        }
    }

    console.log(`Cleaned ${cleaned} rows (where ID was empty).`);
    if (cleaned > 0) {
        await sheet.saveUpdatedCells();
    }
}

cleanRows();
