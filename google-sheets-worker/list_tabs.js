const { GoogleSpreadsheet } = require('google-spreadsheet');
const { JWT } = require('google-auth-library');
require('dotenv').config();
const creds = require('./credentials.json');

async function listTabs() {
    const serviceAccountAuth = new JWT({
        email: creds.client_email,
        key: creds.private_key,
        scopes: ['https://www.googleapis.com/auth/spreadsheets'],
    });

    const doc = new GoogleSpreadsheet(process.env.SHEET_ID, serviceAccountAuth);
    await doc.loadInfo();

    console.log("Sheet Tabs:");
    doc.sheetsByIndex.forEach(sheet => {
        console.log(`- ${sheet.title}`);
    });
}

listTabs();
