require('dotenv').config();
const { GoogleSpreadsheet } = require('google-spreadsheet');
const { JWT } = require('google-auth-library');
const creds = require('./credentials.json');

async function listTabs() {
    const auth = new JWT({
        email: creds.client_email,
        key: creds.private_key,
        scopes: ['https://www.googleapis.com/auth/spreadsheets'],
    });

    const doc = new GoogleSpreadsheet(process.env.SHEET_ID, auth); // Default ID
    await doc.loadInfo();
    console.log(`Title: ${doc.title}`);
    console.log("Tabs:");
    doc.sheetsByIndex.forEach(s => console.log(`[${s.index}] '${s.title}' (Rows: ${s.rowCount})`));
}

listTabs();
