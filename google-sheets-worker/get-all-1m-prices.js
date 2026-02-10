require('dotenv').config();
const { GoogleSpreadsheet } = require('google-spreadsheet');
const { JWT } = require('google-auth-library');
const creds = require('./credentials.json');

async function getAllPrices1m() {
    const serviceAccountAuth = new JWT({
        email: creds.client_email,
        key: creds.private_key,
        scopes: ['https://www.googleapis.com/auth/spreadsheets'],
    });

    const doc = new GoogleSpreadsheet(process.env.SHEET_ID, serviceAccountAuth);
    await doc.loadInfo();
    const sheet = doc.sheetsByTitle['10x10mm'];

    // Load a larger chunk to ensure we cover all widths. 
    // 19cm probably starts earliest? Or is it ordered?
    // Let's load 300 rows.
    await sheet.loadCells('A1:H300');

    // We look for headers in Column F (Index 5) or similar based on previous find.
    // Previous found: "10x10mm 50cm" at Row 15, Col 6 (G). Wait, Col 6 is G. (0=A, 5=F, 6=G).
    // "10x10mm 30cm" at Row 43, Col 6.
    // "10x10mm 1mt..." at Row 72, Col 6.
    // So headers seem to be in Column G (Index 6) or F (Index 5).

    // Let's scan Col G for patterns "10x10mm ..."
    const sections = [];

    for (let r = 0; r < 300; r++) {
        // Check Col G (6) and Col E (4) / F (5) just in case
        // Row 110 was found at Col 5.
        [4, 5, 6].forEach(c => {
            const val = sheet.getCell(r, c).value;
            if (typeof val === 'string' && val.toLowerCase().includes('10x10mm')) {
                // Extract Width Name
                // "10x10mm 50cm" -> "50cm"
                let name = val.replace(/10x10mm\s*/i, '').trim();

                // Only add if not already added (sometimes headers span merged cells)
                if (!sections.find(s => s.row === r)) {
                    sections.push({ row: r, name: name, col: c });
                }
            }
        });
    }

    // Sort sections by row
    sections.sort((a, b) => a.row - b.row);

    console.log("Found sections:", sections.map(s => `${s.name} (Row ${s.row + 1})`).join(', '));

    // Now search for 1m price in each section
    // Range is from section.row to next_section.row (or end).

    const results = [];

    for (let i = 0; i < sections.length; i++) {
        const sec = sections[i];
        const startRow = sec.row;
        const endRow = (i < sections.length - 1) ? sections[i + 1].row : 300;

        let bestPrice = Infinity;
        let found = false;

        for (let r = startRow; r < endRow; r++) {
            const meters = sheet.getCell(r, 1).value; // Col B
            if (meters === 1) {
                const price = sheet.getCell(r, 3).value; // Col D
                if (typeof price === 'number') {
                    if (price < bestPrice) bestPrice = price;
                    found = true;
                }
            }
        }

        if (found) {
            results.push({ width: sec.name, price: bestPrice });
        }
    }

    // Deduplicate and Sort results?
    // 19cm, 30cm, 40cm, etc.
    // Helper to sort by numeric width value
    function parseWidth(w) {
        if (w.includes('mt') || w.includes('metro')) return parseFloat(w) * 100;
        return parseFloat(w);
    }

    results.sort((a, b) => parseWidth(a.width) - parseWidth(b.width));

    console.log("\n--- LISTADO 1 METRO (10x10mm) ---");
    results.forEach(r => {
        console.log(`${r.width}: $${r.price.toLocaleString('es-AR')}`);
    });
}

getAllPrices1m();
