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

    const sheet = doc.sheetsByTitle['15x15mm'];
    if (!sheet) {
        console.error("❌ Sheet not found!");
        process.exit(1);
    }

    // Inspect Row 12 (Index 11)
    await sheet.loadCells('A12:T12'); // Checking columns relevant to the user request

    const cellA = sheet.getCell(11, 0);  // Name/Desc
    const cellD = sheet.getCell(11, 3);  // Column D
    const cellN = sheet.getCell(11, 13); // Column N
    const cellO = sheet.getCell(11, 14); // Column O
    const cellT = sheet.getCell(11, 19); // Column T

    console.log("--- ROW 12 ANALYSIS ---");
    console.log("A12 (Desc):", cellA.value);
    console.log("N12 (Input?): Val:", cellN.value, "| Formula:", cellN.formula);
    console.log("O12 (Num):    Val:", cellO.value, "| Formula:", cellO.formula);
    console.log("D12 (Denom):  Val:", cellD.value, "| Formula:", cellD.formula);
    console.log("T12 (Result): Val:", cellT.value, "| Formula:", cellT.formula);
}

inspect();
