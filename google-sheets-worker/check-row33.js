require('dotenv').config();
const { GoogleSpreadsheet } = require('google-spreadsheet');
const { JWT } = require('google-auth-library');
const creds = require('./credentials.json');

async function checkRow33() {
    const serviceAccountAuth = new JWT({
        email: creds.client_email,
        key: creds.private_key,
        scopes: ['https://www.googleapis.com/auth/spreadsheets'],
    });

    const doc = new GoogleSpreadsheet(process.env.SHEET_ID_STOCK, serviceAccountAuth);
    await doc.loadInfo();

    // 1. Inspect PEDIDOS Row 33 (Index 32)
    const sheetOrders = doc.sheetsByTitle['PEDIDOS'];
    const r = 32; // Row 33
    await sheetOrders.loadCells('A33:N33');

    const product = sheetOrders.getCell(r, 2).value; // Col C
    const leftover = sheetOrders.getCell(r, 10).value; // Col K

    console.log("--- PEDIDOS ROW 33 ---");
    console.log(`Product: ${product}`);
    console.log(`Leftover (K): ${leftover}`);

    // 2. Identify and Inspect Stock Tab
    let stockTabName = null;
    const pStr = String(product).toLowerCase();

    // Simple heuristic mapping based on Product Name
    if (pStr.includes('50x50') && pStr.includes('2mm')) {
        // Find "50x50mm 2mm"
        doc.sheetsByIndex.forEach(s => {
            if (s.title.includes('50x50') && s.title.includes('2mm')) stockTabName = s.title;
        });
    } else if (pStr.includes('50x50') && pStr.includes('1,6')) {
        doc.sheetsByIndex.forEach(s => {
            if (s.title.includes('50x50') && s.title.includes('1,6')) stockTabName = s.title;
        });
    } else if (pStr.includes('20x20') && pStr.includes('1,2')) {
        // ... (Add more mappings as needed)
    } else if (pStr.includes('50x50')) {
        // Fallback for 50x50 generic
        doc.sheetsByIndex.forEach(s => {
            if (s.title.includes('50x50') && s.title.includes('2mm')) stockTabName = s.title; // Default to 2mm if not specified?
        });
    }

    if (stockTabName) {
        console.log(`\nInspecting Stock Tab: "${stockTabName}"`);
        const stockSheet = doc.sheetsByTitle[stockTabName];
        await stockSheet.loadCells('A1:Z100');

        console.log("--- SEARCHING FOR 3.5 ---");
        let found = false;
        for (let row = 0; row < 100; row++) {
            for (let col = 0; col < 20; col++) {
                const val = stockSheet.getCell(row, col).value;
                // Check exact match or close float?
                if (val == 3.5) {
                    console.log(`✅ FOUND 3.5 at Row ${row + 1}, Col ${col}`);
                    found = true;
                }
            }
        }
        if (!found) console.log("❌ Value 3.5 NOT found.");

    } else {
        console.log(`⚠️ Could not automatically identify Stock Tab for product: "${product}"`);
        console.log("Available tabs:");
        doc.sheetsByIndex.forEach(s => console.log(`- ${s.title}`));
    }
}

checkRow33();
