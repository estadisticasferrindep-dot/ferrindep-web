const { GoogleSpreadsheet } = require('google-spreadsheet');
const { JWT } = require('google-auth-library');
require('dotenv').config();

const SHEET_TAB_NAME = '50x50mm';

async function verifyRow84() {
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

        const rows = await sheet.getRows({ limit: 1, offset: 84 });
        const row = rows[0];

        console.log(`Row 85 (Index 84) Raw Data: [${row._rawData.join(' | ')}]`);

    } catch (e) {
        console.error(e);
    }
}

verifyRow84();
