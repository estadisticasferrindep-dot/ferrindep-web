require('dotenv').config();
const { GoogleSpreadsheet } = require('google-spreadsheet');
const { JWT } = require('google-auth-library');
const creds = require('./credentials.json');

async function flagMissingStock() {
    const serviceAccountAuth = new JWT({
        email: creds.client_email,
        key: creds.private_key,
        scopes: ['https://www.googleapis.com/auth/spreadsheets'],
    });

    const doc = new GoogleSpreadsheet(process.env.SHEET_ID_STOCK, serviceAccountAuth);
    await doc.loadInfo();

    const sheet = doc.sheetsByTitle['PEDIDOS'];
    await sheet.loadCells('A27:O30'); // Load Rows 27-30 to be safe (Indexes 26-29)

    // Find the row with Leftover 6.5 (Col K / Index 10)
    // around the area user mentioned.

    let targetRow = -1;

    for (let r = 26; r < 30; r++) { // Rows 27 to 30
        const leftover = sheet.getCell(r, 10).value; // K
        if (leftover == 6.5) {
            console.log(`Found target at Row ${r + 1} (Leftover: ${leftover})`);
            targetRow = r;
            break;
        }
    }

    if (targetRow !== -1) {
        const cellNote = sheet.getCell(targetRow, 13); // Column N is Index 13 (A=0 ... N=13)
        cellNote.value = "no se anotó sobrante";
        await sheet.saveUpdatedCells();
        console.log(`Updated Row ${targetRow + 1} Column N with note.`);
    } else {
        console.log("Could not find row with matching leftover 6.5 in range 27-30.");
    }
}

flagMissingStock();
