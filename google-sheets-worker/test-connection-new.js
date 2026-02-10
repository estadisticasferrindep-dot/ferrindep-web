require('dotenv').config();
const { GoogleSpreadsheet } = require('google-spreadsheet');
const { JWT } = require('google-auth-library');
const creds = require('./credentials.json');

// New Stock Sheet ID provided by user
const NEW_SHEET_ID = '1pBIByoZ1i6pUNSn7uw2nYMTxMNWQ7bka-btA85AHYY8';

async function testAccess() {
    const serviceAccountAuth = new JWT({
        email: creds.client_email,
        key: creds.private_key,
        scopes: ['https://www.googleapis.com/auth/spreadsheets'],
    });

    console.log(`Attempting to connect to Sheet ID: ${NEW_SHEET_ID}`);

    const doc = new GoogleSpreadsheet(NEW_SHEET_ID, serviceAccountAuth);

    try {
        await doc.loadInfo();
        console.log(`✅ SUCCESS! Connected to sheet: "${doc.title}"`);
        console.log(`   Spreadsheet Locale: ${doc.locale}`);
        console.log(`   Sheet Count: ${doc.sheetCount}`);

        console.log("   --- Sheets ---");
        doc.sheetsByIndex.forEach(sheet => {
            console.log(`   - [${sheet.index}] ${sheet.title} (Rows: ${sheet.rowCount}, Cols: ${sheet.columnCount})`);
        });

    } catch (error) {
        console.error("❌ FAILED to connect.");
        console.error("Error details:", error.message);
        console.error("Make sure you shared the sheet with:", creds.client_email);
    }
}

testAccess();
