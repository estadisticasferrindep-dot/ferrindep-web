require('dotenv').config();
const { GoogleSpreadsheet } = require('google-spreadsheet');
const { JWT } = require('google-auth-library');
const creds = require('./credentials.json');

async function inspect() {
    const serviceAccountAuth = new JWT({
        email: creds.client_email,
        key: creds.private_key,
        scopes: ['https://www.googleapis.com/auth/spreadsheets'],
    });

    const doc = new GoogleSpreadsheet(process.env.SHEET_ID, serviceAccountAuth);
    await doc.loadInfo();

    // Find sheet by name
    const sheet = doc.sheetsByTitle['15x15mm'];
    if (!sheet) {
        console.error("❌ Sheet '15x15mm' not found!");
        process.exit(1);
    }

    // Load cells in range (Column S and T, e.g., rows 10 to 20 for inspection)
    // T12 is around row 12. Let's load rows 10-30 to see context.
    await sheet.loadCells('S10:T30');

    // T12 is at index (row=11, col=19) because 0-indexed.
    const cellT12 = sheet.getCell(11, 19);
    const cellS12 = sheet.getCell(11, 18);

    console.log("--- INSPECTION ---");
    console.log("Cell T12 Value:", cellT12.value);
    console.log("Cell T12 Formula:", cellT12.formula);
    console.log("Cell S12 Value (ML ID):", cellS12.value);
}

inspect();
