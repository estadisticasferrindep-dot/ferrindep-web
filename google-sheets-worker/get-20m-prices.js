require('dotenv').config();
const { GoogleSpreadsheet } = require('google-spreadsheet');
const { JWT } = require('google-auth-library');
const creds = require('./credentials.json');

async function getPrices() {
    const serviceAccountAuth = new JWT({
        email: creds.client_email,
        key: creds.private_key,
        scopes: ['https://www.googleapis.com/auth/spreadsheets'],
    });

    const doc = new GoogleSpreadsheet(process.env.SHEET_ID, serviceAccountAuth);
    await doc.loadInfo();
    const sheet = doc.sheetsByTitle['10x10mm'];

    // Sections start roughly at:
    // 50cm: Row 15
    // 30cm: Row 43
    // 1m: Row 72

    // We'll search in ranges. 
    // 50cm: 15 to 40?
    // 30cm: 43 to 70?
    // 1m: 72 to 108?

    await sheet.loadCells('A1:D120');

    const sections = [
        { name: '30cm', startRow: 43, endRow: 70 },
        { name: '50cm', startRow: 15, endRow: 40 },
        { name: '1m', startRow: 72, endRow: 108 }
    ];

    const results = [];

    for (const sec of sections) {
        // Find 20m row
        let foundPrices = [];
        for (let r = sec.startRow; r < sec.endRow; r++) {
            const meters = sheet.getCell(r, 1).value; // Col B
            if (meters === 20) {
                const price = sheet.getCell(r, 3).value; // Col D
                if (typeof price === 'number') {
                    foundPrices.push(price);
                }
            }
        }

        // Sort prices and pick lowest (Common)
        foundPrices.sort((a, b) => a - b);

        if (foundPrices.length > 0) {
            results.push({
                width: sec.name,
                price: foundPrices[0]
            });
        } else {
            results.push({ width: sec.name, price: 'Not Found' });
        }
    }

    console.log("--- PRICES FOR 20M ROLLS ---");
    results.forEach(r => {
        console.log(`${r.width}: $${r.price.toLocaleString('es-AR')}`);
    });
}

getPrices();
