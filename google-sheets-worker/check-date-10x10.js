require('dotenv').config();
const { GoogleSpreadsheet } = require('google-spreadsheet');
const { JWT } = require('google-auth-library');
const creds = require('./credentials.json');

async function verifyDates10x10() {
    const serviceAccountAuth = new JWT({
        email: creds.client_email,
        key: creds.private_key,
        scopes: ['https://www.googleapis.com/auth/spreadsheets'],
    });

    const doc = new GoogleSpreadsheet(process.env.SHEET_ID_STOCK, serviceAccountAuth);
    await doc.loadInfo();

    const sheet = doc.sheetsByTitle['10x10mm'];
    console.log(`Inspecting "${sheet.title}" around Row 16...`);

    // Load rows 10-20 to check the 3.5m entries
    await sheet.loadCells('A10:K20');

    // Dump rows to find where the date is.
    // User said "3 de enero".

    for (let r = 10; r < 20; r++) { // Rows 11-20
        const rowVals = [];
        for (let c = 0; c < 11; c++) {
            const val = sheet.getCell(r, c).formattedValue; // Get string representation
            if (val) rowVals.push(`[${c}] ${val}`);
        }
        if (rowVals.length) console.log(`Row ${r + 1}: ${rowVals.join(' | ')}`);
    }
}

verifyDates10x10();
