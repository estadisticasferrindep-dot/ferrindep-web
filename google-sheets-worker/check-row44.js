require('dotenv').config();
const { GoogleSpreadsheet } = require('google-spreadsheet');
const { JWT } = require('google-auth-library');
const creds = require('./credentials.json');

async function checkRow44AndStock() {
    const serviceAccountAuth = new JWT({
        email: creds.client_email,
        key: creds.private_key,
        scopes: ['https://www.googleapis.com/auth/spreadsheets'],
    });

    const doc = new GoogleSpreadsheet(process.env.SHEET_ID_STOCK, serviceAccountAuth);
    await doc.loadInfo();

    // 1. Inspect PEDIDOS Row 44 (Index 43)
    const sheetOrders = doc.sheetsByTitle['PEDIDOS'];
    await sheetOrders.loadCells('A44:N44');
    const r = 43;
    const colC = sheetOrders.getCell(r, 2).value; // Product
    const colJ = sheetOrders.getCell(r, 9).value; // Source
    const colK = sheetOrders.getCell(r, 10).value; // Leftover

    console.log("--- PEDIDOS ROW 44 ---");
    console.log(`Product: ${colC}`);
    console.log(`Source (J): ${colJ}`);
    console.log(`Leftover (K): ${colK}`);

    // 2. Inspect '50x50mm 2mm' (Index 14)
    // Or verify name specifically
    let stockSheet = null;
    doc.sheetsByIndex.forEach(s => {
        if (s.title.includes('50x50') && s.title.includes('2mm')) stockSheet = s;
    });

    if (stockSheet) {
        console.log(`\nInspecting Stock Sheet: "${stockSheet.title}"`);
        await stockSheet.loadCells('A1:Z100');

        // Look for value 8.5
        // User implies it should be in a specific column for 70cm width?
        // I'll scan headers for "70cm" just in case.

        console.log("--- HEADERS (50x50) ---");
        for (let hr = 0; hr < 5; hr++) {
            const headers = [];
            for (let c = 0; c < 20; c++) headers.push(`[${c}] ${stockSheet.getCell(hr, c).value}`);
            // console.log(`R${hr+1}: ${headers.join(', ')}`); 
            // (Commented out to reduce noise, unless needed)
        }

        console.log("--- SEARCHING FOR 8.5 ---");
        let found = false;
        for (let row = 0; row < 100; row++) {
            for (let col = 0; col < 20; col++) {
                if (stockSheet.getCell(row, col).value == 8.5) {
                    console.log(`FOUND 8.5 at Row ${row + 1}, Col ${col}`);
                    found = true;
                }
            }
        }
        if (!found) console.log("Value 8.5 NOT found.");

    } else {
        console.log("Sheet '50x50mm 2mm' NOT found.");
    }
}

checkRow44AndStock();
