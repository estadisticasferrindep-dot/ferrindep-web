require('dotenv').config();
const { GoogleSpreadsheet } = require('google-spreadsheet');
const { JWT } = require('google-auth-library');
const creds = require('./credentials.json');

async function check20x20Real() {
    const serviceAccountAuth = new JWT({
        email: creds.client_email,
        key: creds.private_key,
        scopes: ['https://www.googleapis.com/auth/spreadsheets'],
    });

    const doc = new GoogleSpreadsheet(process.env.SHEET_ID_STOCK, serviceAccountAuth);
    await doc.loadInfo();

    const sheet = doc.sheetsByTitle['20x20mm']; // Index 9
    console.log(`Inspecting "${sheet.title}"...`);

    // Load broader range to find value
    // User said column corresponding to "1.2mm".
    // I need to check Headers to identify which column is "1.2mm".

    await sheet.loadCells('A1:Z100');

    // Find "1.2mm" or "1.2" in headers (Row 1-5)
    let targetCol = -1;
    console.log("--- HEADERS ---");
    for (let r = 0; r < 5; r++) {
        const rowVals = [];
        for (let c = 0; c < 20; c++) {
            const val = sheet.getCell(r, c).value;
            if (val) {
                rowVals.push(`[${c}] ${val}`);
                if (String(val).includes('1,2') || String(val).includes('1.2')) {
                    console.log(`MATCH header at R${r + 1}C${c}: ${val}`);
                    targetCol = c;
                }
            }
        }
        if (rowVals.length) console.log(`R${r + 1}: ${rowVals.join(', ')}`);
    }

    // Check for 6.5 in the target column (or any column if not found)
    console.log("\n--- SEARCHING FOR 6.5 ---");
    let found = false;

    if (targetCol === -1) console.log("Warning: '1.2mm' column not explicitly matched in headers. Scanning ALL columns.");

    for (let r = 0; r < 100; r++) {
        // Search Target Col first
        if (targetCol !== -1) {
            const val = sheet.getCell(r, targetCol).value;
            if (val == 6.5) {
                console.log(`FOUND 6.5 in TARGET COLUMN at Row ${r + 1}, Col ${targetCol}`);
                found = true;
            }
        }

        // Search others just in case
        for (let c = 0; c < 20; c++) {
            const val = sheet.getCell(r, c).value;
            if (val == 6.5) {
                if (c !== targetCol) {
                    console.log(`FOUND 6.5 in OTHER column at Row ${r + 1}, Col ${c} (${String.fromCharCode(65 + c)})`);
                    found = true;
                }
            }
        }
    }
}

check20x20Real();
