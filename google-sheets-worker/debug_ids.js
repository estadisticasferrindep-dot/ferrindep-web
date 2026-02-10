const { GoogleSpreadsheet } = require('google-spreadsheet');
const { JWT } = require('google-auth-library');
require('dotenv').config();

const SHEET_TAB_NAME = '50x50mm';

async function debugIds() {
    try {
        const creds = require('./credentials.json');
        const serviceAccountAuth = new JWT({
            email: creds.client_email,
            key: creds.private_key,
            scopes: ['https://www.googleapis.com/auth/spreadsheets'],
        });

        const doc = new GoogleSpreadsheet(process.env.SHEET_ID, serviceAccountAuth);
        await doc.loadInfo();

        console.log(`Env SHEET_ID: ${process.env.SHEET_ID}`);
        console.log(`Doc Title: ${doc.title}`);

        const sheet = doc.sheetsByTitle[SHEET_TAB_NAME];
        if (sheet) {
            console.log(`Tab '${SHEET_TAB_NAME}' found. ID (GID): ${sheet.sheetId}`);
            console.log(`Row Count: ${sheet.rowCount}`);
        } else {
            console.log(`Tab '${SHEET_TAB_NAME}' NOT found.`);
            console.log('Available Tabs:', doc.sheetsByIndex.map(s => `${s.title} (${s.sheetId})`).join(', '));
        }

    } catch (e) {
        console.error(e);
    }
}

debugIds();
