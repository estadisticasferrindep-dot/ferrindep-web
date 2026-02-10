require('dotenv').config();
const { GoogleSpreadsheet } = require('google-spreadsheet');
const { JWT } = require('google-auth-library');
const creds = require('./credentials.json');

async function inspectHeaders() {
    const serviceAccountAuth = new JWT({
        email: creds.client_email,
        key: creds.private_key,
        scopes: ['https://www.googleapis.com/auth/spreadsheets'],
    });

    const doc = new GoogleSpreadsheet(process.env.SHEET_ID_STOCK, serviceAccountAuth);
    await doc.loadInfo();
    const sheet = doc.sheetsByTitle['PEDIDOS'];

    // User says orders start at Row 24.
    // So headers likely at Row 23? Or Row 24 is data and headers are above?
    // Let's inspect 20-25.

    await sheet.loadCells('A20:Z26');

    console.log("--- ROW INSPECTION (21-25) ---");
    for (let r = 20; r < 25; r++) { // Index 20 = Row 21
        const rowNum = r + 1;
        const rowVals = [];
        for (let c = 0; c < 15; c++) { // First 15 cols
            const val = sheet.getCell(r, c).value;
            if (val !== null) rowVals.push(`Col${c}: "${val}"`);
        }
        if (rowVals.length > 0) {
            console.log(`ROW ${rowNum}: ${rowVals.join(' | ')}`);
        }
    }
}

inspectHeaders();
