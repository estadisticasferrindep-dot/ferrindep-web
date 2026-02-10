require('dotenv').config();
const { GoogleSpreadsheet } = require('google-spreadsheet');
const { JWT } = require('google-auth-library');
const creds = require('./credentials.json');

async function replicate() {
    const serviceAccountAuth = new JWT({
        email: creds.client_email,
        key: creds.private_key,
        scopes: ['https://www.googleapis.com/auth/spreadsheets'],
    });

    const doc = new GoogleSpreadsheet(process.env.SHEET_ID, serviceAccountAuth);
    await doc.loadInfo();

    const sheet = doc.sheetsByTitle['15x15mm'];
    if (!sheet) {
        console.error("❌ Sheet '15x15mm' not found!");
        process.exit(1);
    }

    // Load all cells. Be careful with very large sheets, but for 1000 rows it's fine.
    console.log(`Loading cells for sheet: ${sheet.title} (${sheet.rowCount} rows)...`);
    await sheet.loadCells();

    let updatesCount = 0;

    // Start from row 12 (index 11)
    for (let i = 11; i < sheet.rowCount; i++) {
        const cellS = sheet.getCell(i, 18); // Column S (ML ID)
        const cellT = sheet.getCell(i, 19); // Column T (Target)

        const mlId = cellS.value;

        // Check if S has a value that looks like an ID (not empty)
        if (mlId && String(mlId).trim() !== '') {
            const rowNum = i + 1; // 1-based row number for formula
            const newFormula = `=O${rowNum}/D${rowNum}`;

            // Only update if different to avoid unnecessary writes, buuuut for formatting we might want to enforce it.
            // Let's just set it.
            if (cellT.formula !== newFormula) {
                cellT.formula = newFormula;
            }

            // APPLY FORMATTING
            // 1. Boolean format: Percent
            cellT.numberFormat = { type: 'PERCENT', pattern: '0.00%' };

            // 2. Text format: Size 11, Black
            cellT.textFormat = {
                fontSize: 11,
                foregroundColor: { red: 0, green: 0, blue: 0 } // Black
            };

            updatesCount++; // Always increment since we are enforcing format now
        }
    }

    if (updatesCount > 0) {
        console.log(`Saving ${updatesCount} updates...`);
        await sheet.saveUpdatedCells();
        console.log("✅ Done!");
    } else {
        console.log("No updates needed (formulas likely already there).");
    }
}

replicate();
