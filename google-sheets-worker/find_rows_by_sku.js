require('dotenv').config();
const { GoogleSpreadsheet } = require('google-spreadsheet');
const { JWT } = require('google-auth-library');
const creds = require('./credentials.json');

const TARGET_TAB = 'MET DESPLE';

// Define the batches
const BATCHES = [
    { name: '1.20m', start: 5927, end: 5949 },
];

async function findRows() {
    const serviceAccountAuth = new JWT({
        email: creds.client_email,
        key: creds.private_key,
        scopes: ['https://www.googleapis.com/auth/spreadsheets'],
    });

    const sheetID = process.env.SHEET_ID_PRICING || process.env.SHEET_ID;
    const doc = new GoogleSpreadsheet(sheetID, serviceAccountAuth);

    try {
        await doc.loadInfo();
        const sheet = doc.sheetsByTitle[TARGET_TAB];
        if (!sheet) { console.error(`Tab '${TARGET_TAB}' not found`); return; }

        console.log(`Scanning '${TARGET_TAB}'...`);

        // Load Columns A (SKU), B (Len), E (Price), T (Margin), Z (Financing Info)
        // A=0, B=1, E=4, T=19, Z=25. 
        // We'll just load A1:Z1000 to be safe and cover everything.
        const MAX_ROWS = 1000;
        await sheet.loadCells(`A1:Z${MAX_ROWS}`);

        for (const batch of BATCHES) {
            console.log(`\n--- Batch: ${batch.name} (SKU ${batch.start}-${batch.end}) ---`);
            let firstRow = null;
            let lastRow = null;
            const foundRows = [];

            for (let i = 0; i < MAX_ROWS; i++) {
                const cellSKU = sheet.getCell(i, 0); // Col A
                const val = cellSKU.value;

                if (typeof val === 'number' && val >= batch.start && val <= batch.end) {
                    const rowNum = i + 1;
                    if (firstRow === null) firstRow = rowNum;
                    lastRow = rowNum;

                    const cellLen = sheet.getCell(i, 1); // B
                    const cellPrice = sheet.getCell(i, 4); // E
                    const cellMargin = sheet.getCell(i, 19); // T
                    const cellFinancing = sheet.getCell(i, 25); // Z (Index 25)

                    foundRows.push({
                        row: rowNum,
                        sku: val,
                        len: cellLen.value,
                        price: cellPrice.value,
                        margin: cellMargin.value,
                        financing: cellFinancing.value // e.g. "3 Cuotas"
                    });
                }
            }

            if (foundRows.length > 0) {
                console.log(`Found ${foundRows.length} rows. Range: Row ${firstRow} to ${lastRow}`);
                console.log("Row | SKU  | Len | Price/m   | Margin | Financing (Col Z)");
                foundRows.forEach(r => {
                    const p = typeof r.price === 'number' ? r.price.toFixed(2) : String(r.price);
                    const m = typeof r.margin === 'number' ? (r.margin * 100).toFixed(2) + '%' : String(r.margin);
                    let f = r.financing ? String(r.financing).trim() : '';
                    if (f) f = `[${f}]`; // Bracket if exists

                    console.log(`${r.row.toString().padEnd(3)} | ${r.sku} | ${String(r.len).padEnd(3)} | $${p.padEnd(8)} | ${m.padEnd(6)} | ${f}`);
                });
            } else {
                console.log("No rows found.");
            }
        }

    } catch (e) {
        console.error("Error:", e);
    }
}

findRows();
