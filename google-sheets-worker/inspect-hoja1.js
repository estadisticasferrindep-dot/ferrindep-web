require('dotenv').config();
const { GoogleSpreadsheet } = require('google-spreadsheet');
const { JWT } = require('google-auth-library');
const creds = require('./credentials.json');

async function inspectHoja1() {
    const serviceAccountAuth = new JWT({
        email: creds.client_email,
        key: creds.private_key,
        scopes: ['https://www.googleapis.com/auth/spreadsheets'],
    });

    const doc = new GoogleSpreadsheet(process.env.SHEET_ID_PRICING, serviceAccountAuth);
    await doc.loadInfo();

    const sheet = doc.sheetsByTitle['Hoja1']; // Correct name
    console.log(`Inspecting '${sheet.title}'...`);

    await sheet.loadCells('A1:K20');

    // Dump Row 1 (Headers) and Row 2-5 (Data)
    // User said SKU is in Col H (Index 7)
    console.log("--- ROW 1 (HEADERS?) ---");
    const headers = [];
    for (let c = 0; c < 11; c++) {
        headers.push(`[${c}] ${sheet.getCell(0, c).value}`);
    }
    console.log(headers.join(' | '));

    console.log("\n--- DATA ---");
    for (let r = 1; r < 10; r++) {
        const vals = [];
        for (let c = 0; c < 11; c++) vals.push(sheet.getCell(r, c).value);
        // Only print if Col H (SKU) has something
        const sku = sheet.getCell(r, 7).value;
        if (sku) console.log(`R${r + 1}: ... Col H (SKU): ${sku} | Col A: ${vals[0]} | Col B: ${vals[1]} | Col C: ${vals[2]}`);
    }
}

inspectHoja1();
