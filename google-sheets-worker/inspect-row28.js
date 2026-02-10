require('dotenv').config();
const { GoogleSpreadsheet } = require('google-spreadsheet');
const { JWT } = require('google-auth-library');
const creds = require('./credentials.json');

async function inspectRow28() {
    const serviceAccountAuth = new JWT({
        email: creds.client_email,
        key: creds.private_key,
        scopes: ['https://www.googleapis.com/auth/spreadsheets'],
    });

    const doc = new GoogleSpreadsheet(process.env.SHEET_ID_STOCK, serviceAccountAuth);
    await doc.loadInfo();
    const sheet = doc.sheetsByTitle['PEDIDOS'];

    // Inspect Row 28 (Index 27)
    // User says: Col J (Index 9) -> Source Roll (20m)
    // Col K (Index 10) -> Leftover (6.5m)?
    // And Order size? Usually in Col B (Index 1)? Or implied from Product Name?

    await sheet.loadCells('A28:M28');

    const r = 27; // Row 28

    const colA = sheet.getCell(r, 0).value; // Status/Desc
    const colB = sheet.getCell(r, 1).value; // Qty/Metros?
    const colC = sheet.getCell(r, 2).value; // Product Name
    const colJ = sheet.getCell(r, 9).value; // Source Roll?
    const colK = sheet.getCell(r, 10).value; // Leftover?

    console.log("--- ROW 28 INSPECTION ---");
    console.log(`Col A: ${colA}`);
    console.log(`Col B: ${colB}`);
    console.log(`Col C: ${colC}`);
    console.log(`Col J (Source): ${colJ}`);
    console.log(`Col K (Leftover): ${colK}`);
}

inspectRow28();
