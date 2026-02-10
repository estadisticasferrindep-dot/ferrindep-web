require('dotenv').config();
const { GoogleSpreadsheet } = require('google-spreadsheet');
const { JWT } = require('google-auth-library');
const creds = require('./credentials.json');

async function findTabAndInspect() {
    const serviceAccountAuth = new JWT({
        email: creds.client_email,
        key: creds.private_key,
        scopes: ['https://www.googleapis.com/auth/spreadsheets'],
    });

    const doc = new GoogleSpreadsheet(process.env.SHEET_ID_STOCK, serviceAccountAuth);
    await doc.loadInfo();

    // List all sheets to find the correct index for "20x20mm 1,2mm"
    console.log("--- SHEET LIST ---");
    let targetSheet = null;

    doc.sheetsByIndex.forEach((s, i) => {
        // Normalize to find match
        if (s.title.includes('20x20mm') && s.title.includes('1,2mm')) {
            console.log(`[MATCH] Index ${i}: ${s.title}`);
            targetSheet = s;
        } else {
            // console.log(`Index ${i}: ${s.title}`);
        }
    });

    if (targetSheet) {
        console.log(`Inspecting ${targetSheet.title}...`);
        await targetSheet.loadCells('A1:K100'); // Load broader range

        // Look for 6.5
        let found = false;
        for (let r = 0; r < 100; r++) {
            for (let c = 0; c < 11; c++) { // Columns A-K
                const val = targetSheet.getCell(r, c).value;
                if (val == 6.5) {
                    console.log(`FOUND 6.5 at Row ${r + 1}, Col ${c} (${String.fromCharCode(65 + c)})`);
                    found = true;
                }
            }
        }

        if (!found) console.log("Value 6.5 NOT found in top 100 rows.");
    } else {
        console.log("Sheet '20x20mm 1,2mm' NOT found.");
    }
}

findTabAndInspect();
