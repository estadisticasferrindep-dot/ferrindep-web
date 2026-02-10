require('dotenv').config();
const { GoogleSpreadsheet } = require('google-spreadsheet');
const { JWT } = require('google-auth-library');
const creds = require('./credentials.json');

async function accessSpreadsheet() {
    const SHEET_ID = process.env.SHEET_ID; // Will be set via .env or passed in
    if (!SHEET_ID) {
        console.error("❌ ERROR: SHEET_ID is missing.");
        console.log("Please provide the Google Sheet ID (the long string in the URL).");
        process.exit(1);
    }

    const serviceAccountAuth = new JWT({
        email: creds.client_email,
        key: creds.private_key,
        scopes: ['https://www.googleapis.com/auth/spreadsheets'],
    });

    const doc = new GoogleSpreadsheet(SHEET_ID, serviceAccountAuth);

    try {
        console.log("Connecting to Google Sheet...");
        await doc.loadInfo(); // loads document properties and worksheets
        console.log(`✅ SUCCESS! Connected to sheet: "${doc.title}"`);
        console.log(`- SpreadSheet ID: ${doc.spreadsheetId}`);
        console.log(`- Sheet Count: ${doc.sheetCount}`);
    } catch (error) {
        console.error("❌ CONNECTION FAILED:", error.message);
        if (error.message.includes('Hv404')) {
            console.error("  -> This usually means the Sheet ID is wrong OR the bot email hasn't been invited.");
        } else if (error.message.includes('403')) {
            console.error("  -> This usually means the bot email (" + creds.client_email + ") does not have permission.");
            console.error("  -> PLEASE SHARE THE SHEET WITH: " + creds.client_email);
        }
    }
}

accessSpreadsheet();
