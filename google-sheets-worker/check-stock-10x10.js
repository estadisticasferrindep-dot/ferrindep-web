require('dotenv').config();
const { GoogleSpreadsheet } = require('google-spreadsheet');
const { JWT } = require('google-auth-library');
const creds = require('./credentials.json');

async function check10x10Stock() {
    const serviceAccountAuth = new JWT({
        email: creds.client_email,
        key: creds.private_key,
        scopes: ['https://www.googleapis.com/auth/spreadsheets'],
    });

    const doc = new GoogleSpreadsheet(process.env.SHEET_ID_STOCK, serviceAccountAuth);
    await doc.loadInfo();

    // Tab '10x10mm' is Index 7
    const sheet = doc.sheetsByTitle['10x10mm'];
    console.log(`Inspecting "${sheet.title}"...`);

    await sheet.loadCells('A1:K100');

    // Look for 3.5
    // User ordered "10x10 mm 50 cm".
    // So likely in a column for "50cm"?
    // I'll scan for 3.5 anywhere.

    console.log("--- SEARCHING FOR 3.5 ---");
    let found = false;
    for (let r = 0; r < 100; r++) {
        for (let c = 0; c < 11; c++) {
            const val = sheet.getCell(r, c).value;
            if (val == 3.5) {
                console.log(`FOUND 3.5 at Row ${r + 1}, Col ${c} (${String.fromCharCode(65 + c)})`);
                found = true;
            }
        }
    }
    if (!found) console.log("Value 3.5 NOT found.");
}

check10x10Stock();
