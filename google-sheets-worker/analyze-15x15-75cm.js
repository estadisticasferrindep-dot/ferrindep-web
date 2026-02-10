require('dotenv').config();
const { GoogleSpreadsheet } = require('google-spreadsheet');
const { JWT } = require('google-auth-library');
const creds = require('./credentials.json');

async function analyze15x75Fixed() {
    const serviceAccountAuth = new JWT({
        email: creds.client_email,
        key: creds.private_key,
        scopes: ['https://www.googleapis.com/auth/spreadsheets'],
    });

    const doc = new GoogleSpreadsheet(process.env.SHEET_ID, serviceAccountAuth);
    await doc.loadInfo();
    const sheet = doc.sheetsByTitle['15x15mm'];

    await sheet.loadCells('A41:F60');

    const items = [];

    for (let r = 40; r < 60; r++) {
        const metros = sheet.getCell(r, 1).value;
        const valE = sheet.getCell(r, 4).value; // Col E (Price/m ?)
        const desc = sheet.getCell(r, 0).value;

        if (typeof metros === 'number') {
            // If valE is null, assume checking failed or empty
            const price = (typeof valE === 'number') ? valE : 0;
            items.push({ row: r + 1, metros, price, desc, rawPrice: valE });
        }
    }

    items.sort((a, b) => a.metros - b.metros);

    console.log("| Row | Metros | Col E ($/m) | Check (Decreasing $/m) |");
    console.log("|---|---|---|---|");

    let prev = null;
    items.forEach(curr => {
        let status = "OK";
        const p = curr.price;

        if (p === 0) {
            status = "❌ NULL/Zero";
        } else if (prev) {
            if (prev.price > 0) {
                if (p > prev.price) {
                    status = "⚠️ RISES (Should be <= Prev)";
                } else if (p < prev.price * 0.9) {
                    // Drop is fine, but huge drop?
                    status = "OK (Drop)";
                } else {
                    status = "OK";
                }
            }
        }

        const pStr = (p > 0) ? `$${p.toLocaleString('es-AR')}` : 'NULL';
        console.log(`| ${curr.row} | ${curr.metros}m | ${pStr} | ${status} |`);

        // Update prev if current is valid price
        if (p > 0) prev = curr;
    });
}

analyze15x75Fixed();
