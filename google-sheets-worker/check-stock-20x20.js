require('dotenv').config();
const { GoogleSpreadsheet } = require('google-spreadsheet');
const { JWT } = require('google-auth-library');
const creds = require('./credentials.json');

async function inspect20x20() {
    const serviceAccountAuth = new JWT({
        email: creds.client_email,
        key: creds.private_key,
        scopes: ['https://www.googleapis.com/auth/spreadsheets'],
    });

    const doc = new GoogleSpreadsheet(process.env.SHEET_ID_STOCK, serviceAccountAuth);
    await doc.loadInfo();

    // Tab name might be "20x20mm" or similar.
    // Test connection output showed: "[6] 20x20mm 1,5mm / 2,1mm" ... 
    // Wait, is there a "20x20mm 1.2mm"?
    // Or "20x20mm"?
    // Looking at Step 441 logs:
    // [5] 20x20mm 1,2mm (Rows: 974, Cols: 702)
    // [6] 20x20mm 1,5mm / 2,1mm (Rows: 974, Cols: 702)
    // [19] 20x50mm

    // User said "pestaña 20 por 20 milímetros... espesor es 1,2 milímetros".
    // So it corresponds to tab index [5]: "20x20mm 1,2mm".

    const sheet = doc.sheetsByIndex[5]; // "20x20mm 1,2mm"
    console.log(`Inspecting Sheet: ${sheet.title}`);

    await sheet.loadCells('A1:J100'); // Load top 100 rows, first 10 cols

    // Find where 6.5 might be stored.
    // Usually these stock sheets have columns like "Rollos", "Metros", etc.
    // I'll print headers (Row 1-5?) and scan for 6.5 value.

    console.log("--- HEADERS (Rows 1-3) ---");
    for (let r = 0; r < 3; r++) {
        const vals = [];
        for (let c = 0; c < 10; c++) vals.push(sheet.getCell(r, c).value);
        console.log(`R${r + 1}: ${vals.join(' | ')}`);
    }

    console.log("\n--- SEARCHING FOR 6.5 ---");
    let found = false;
    for (let r = 0; r < 100; r++) {
        for (let c = 0; c < 10; c++) {
            const val = sheet.getCell(r, c).value;
            if (val === 6.5) {
                console.log(`FOUND 6.5 at Row ${r + 1}, Col ${c} (${String.fromCharCode(65 + c)})`);
                found = true;
            }
        }
    }
    if (!found) console.log("Value 6.5 NOT found in first 100 rows/10 cols.");
}

inspect20x20();
