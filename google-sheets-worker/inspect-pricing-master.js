require('dotenv').config();
const { GoogleSpreadsheet } = require('google-spreadsheet');
const { JWT } = require('google-auth-library');
const creds = require('./credentials.json');

async function inspectPricingMaster() {
    const serviceAccountAuth = new JWT({
        email: creds.client_email,
        key: creds.private_key,
        scopes: ['https://www.googleapis.com/auth/spreadsheets'],
    });

    // Use SHEET_ID_PRICING
    const doc = new GoogleSpreadsheet(process.env.SHEET_ID_PRICING, serviceAccountAuth);
    await doc.loadInfo();

    const sheet = doc.sheetsByTitle['Hoja 1'];
    console.log(`Inspecting '${sheet.title}' in Pricing Sheet...`);

    await sheet.loadCells('A1:J20'); // Headers + some data

    console.log("--- HEADERS ---");
    const headers = [];
    for (let c = 0; c < 10; c++) headers.push(`[${c}] ${sheet.getCell(0, c).value}`);
    console.log(headers.join(' | '));

    console.log("\n--- SAMPLE DATA (Row 2-5) ---");
    for (let r = 1; r < 5; r++) {
        const vals = [];
        for (let c = 0; c < 10; c++) vals.push(sheet.getCell(r, c).value);
        console.log(`R${r + 1}: ${vals.join(' | ')}`);
    }
}

inspectPricingMaster();
