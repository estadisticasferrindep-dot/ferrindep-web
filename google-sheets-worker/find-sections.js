require('dotenv').config();
const { GoogleSpreadsheet } = require('google-spreadsheet');
const { JWT } = require('google-auth-library');
const creds = require('./credentials.json');

async function findSections() {
    const serviceAccountAuth = new JWT({
        email: creds.client_email,
        key: creds.private_key,
        scopes: ['https://www.googleapis.com/auth/spreadsheets'],
    });

    const doc = new GoogleSpreadsheet(process.env.SHEET_ID, serviceAccountAuth);
    await doc.loadInfo();
    const sheet = doc.sheetsByTitle['10x10mm'];

    // Load first column to find headers? Or header row?
    // User previously mentioned "1m de altura que va de fila 75 a 106".
    // This implies vertical stacking of sections.
    // Let's load the first 150 rows of Column A (SKU) and maybe Column B (Metros) or a Description Column?
    // Usually Description is absent or in header. 
    // Let's check headers. The user provided an image earlier of 15x15mm, headers were in Row 1.
    // "15x15mm 30cm", "15x15mm 40cm" in COLUMNS?
    // Wait, the previous image showed "15x15mm 30cm" in COMPRESSED columns?
    // NO, looking at the image (uploaded_media_1769619606024.png), 
    // Row 1 has headers: "15x15mm 30cm" (Col C?), "15x15mm 40cm" (Col E?), etc.
    // BUT the user said "1m de altura que va de fila 75 a 106".
    // This suggests for 10x10mm, they might use horizontal sections too? 
    // OR vertical.
    // "1m ... fila 75 a 106". This is clearly vertical in rows.
    // So 30cm and 50cm must be in other row ranges.
    // Let's search for "30cm", "50cm" in the cells, or infer from headers if they exist.
    // I will load A1:T200 and search for text.

    await sheet.loadCells('A1:K200'); // Load enough to find section titles

    console.log("Scanning 10x10mm sheet for 30cm and 50cm sections...");

    for (let r = 0; r < 200; r++) {
        // Check first few columns for section headers
        for (let c = 0; c < 10; c++) {
            const val = sheet.getCell(r, c).value;
            if (typeof val === 'string' && (val.includes('30cm') || val.includes('50cm') || val.includes('1m') || val.includes('100cm'))) {
                console.log(`Found "${val}" at Row ${r + 1}, Col ${c}`);
            }
        }
        // Also check specific rows?
    }
}

findSections();
