const { GoogleSpreadsheet } = require('google-spreadsheet');
const { JWT } = require('google-auth-library');
require('dotenv').config();

const SHEET_TAB_NAME = '50x50mm';

async function inspect() {
    try {
        const creds = require('./credentials.json');
        const serviceAccountAuth = new JWT({
            email: creds.client_email,
            key: creds.private_key,
            scopes: ['https://www.googleapis.com/auth/spreadsheets'],
        });

        const doc = new GoogleSpreadsheet(process.env.SHEET_ID, serviceAccountAuth);
        await doc.loadInfo();

        const sheet = doc.sheetsByTitle[SHEET_TAB_NAME];
        if (!sheet) {
            console.log(`Tab '${SHEET_TAB_NAME}' NOT found.`);
            console.log('Available tabs:', doc.sheetsByIndex.map(s => s.title).join(', '));
            return;
        }

        console.log(`Tab '${SHEET_TAB_NAME}' found. Verifying Copy...`);

        // Fetch rows around insertion point (Index 85)
        // We want to see Index 84 (SKU 3380), Index 85 (Empty), Index 86 (Next Item)
        const rows = await sheet.getRows({ limit: 5, offset: 84 });
        // offset 84 means start at Index 84 (Row 85 in UI)

        rows.forEach((r, i) => {
            console.log(`Index ${84 + i} (Row ${85 + i}): [${r._rawData.join(' | ')}]`);
        });

    } catch (e) {
        console.error(e);
    }
}

inspect();
