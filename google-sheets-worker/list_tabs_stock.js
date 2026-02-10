const { GoogleSpreadsheet } = require('google-spreadsheet');
const { JWT } = require('google-auth-library');
const creds = require('./credentials.json');
require('dotenv').config();

async function listTabs() {
    const SHEET_ID = process.env.SHEET_ID_STOCK;
    const serviceAccountAuth = new JWT({
        email: creds.client_email,
        key: creds.private_key,
        scopes: ['https://www.googleapis.com/auth/spreadsheets'],
    });

    try {
        const doc = new GoogleSpreadsheet(SHEET_ID, serviceAccountAuth);
        await doc.loadInfo();

        console.log(`Doc Title: ${doc.title}`);
        doc.sheetsByIndex.forEach(s => console.log(s.title));
    } catch (e) {
        console.error(e);
    }
}
listTabs();
