const { GoogleSpreadsheet } = require('google-spreadsheet');
const { JWT } = require('google-auth-library');
const creds = require('./credentials.json');

async function readRange() {
    const VENTAS_SHEET_ID = '1dsPDnS2CJq3zs69SqHtA-5oXhuLv21_Tp7UY_6m9NZw';

    const serviceAccountAuth = new JWT({
        email: creds.client_email,
        key: creds.private_key,
        scopes: ['https://www.googleapis.com/auth/spreadsheets'],
    });

    const doc = new GoogleSpreadsheet(VENTAS_SHEET_ID, serviceAccountAuth);
    await doc.loadInfo();

    const sheet = doc.sheetsByTitle['VVentas'];
    if (!sheet) {
        console.error("Tab 'VVentas' not found!");
        return;
    }

    await sheet.loadCells('A1:P13');

    console.log("--- Raw Data Block (A1:P13) ---");
    // Print header columns for reference
    console.log("   | A | B | C | D | E | F | G | H | I | J | K | L | M | N | O | P |");

    for (let r = 0; r < 13; r++) {
        let rowStr = `R${r + 1} | `;
        for (let c = 0; c < 16; c++) { // A=0 to P=15
            const cell = sheet.getCell(r, c);
            let val = cell.value;
            if (val === null) val = "";
            // Print full value
            let sVal = String(val);
            rowStr += `[${sVal}] `;
        }
        console.log(rowStr);
    }
}

readRange();
