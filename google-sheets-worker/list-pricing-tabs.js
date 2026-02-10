require('dotenv').config();
const { GoogleSpreadsheet } = require('google-spreadsheet');
const { JWT } = require('google-auth-library');
const creds = require('./credentials.json');

async function listPricingTabs() {
    const serviceAccountAuth = new JWT({
        email: creds.client_email,
        key: creds.private_key,
        scopes: ['https://www.googleapis.com/auth/spreadsheets'],
    });

    const doc = new GoogleSpreadsheet(process.env.SHEET_ID_PRICING, serviceAccountAuth);
    await doc.loadInfo();

    console.log("--- PRICING SHEET TABS ---");
    doc.sheetsByIndex.forEach((s, i) => {
        console.log(`[${i}] "${s.title}"`);
    });
}

listPricingTabs();
