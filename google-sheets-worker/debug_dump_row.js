require('dotenv').config();
const { GoogleSpreadsheet } = require('google-spreadsheet');
const { JWT } = require('google-auth-library');
const creds = require('./credentials.json');

const ROW_TO_DUMP = 82;
const RANGE_START = 82;
const RANGE_END = 83;

async function dumpRow() {
    const auth = new JWT({
        email: creds.client_email,
        key: creds.private_key,
        scopes: ['https://www.googleapis.com/auth/spreadsheets'],
    });

    const doc = new GoogleSpreadsheet(process.env.SHEET_ID, auth);
    await doc.loadInfo();
    const sheet = doc.sheetsByTitle['10x10mm'];

    // Load range
    await sheet.loadCells(`A${RANGE_START}:Z${RANGE_END}`);

    console.log(`--- DUMP RANGE ${RANGE_START}-${RANGE_END} ---`);
    for (let r = RANGE_START; r <= RANGE_END; r++) {
        const vals = [];
        const rIdx = r - 1;
        const desc = sheet.getCell(rIdx, 5).value; // F
        if (!desc) continue; // Skip empty rows

        for (let c = 0; c < 20; c++) {
            const cell = sheet.getCell(rIdx, c);
            if (cell.formula) {
                vals.push(`[${c}]=FORMULA:${cell.formula}`);
            } else if (cell.value) {
                vals.push(`[${c}]=${cell.value}`);
            }
        }
        if (vals.length > 0) console.log(`R${r}: ${vals.join(', ')}`);
    }
}

dumpRow();
