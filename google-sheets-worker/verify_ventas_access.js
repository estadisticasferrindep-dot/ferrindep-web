const { GoogleSpreadsheet } = require('google-spreadsheet');
const { JWT } = require('google-auth-library');
const creds = require('./credentials.json');

async function testAccess() {
    const VENTAS_SHEET_ID = '1dsPDnS2CJq3zs69SqHtA-5oXhuLv21_Tp7UY_6m9NZw';

    console.log('Authenticating...');
    const serviceAccountAuth = new JWT({
        email: creds.client_email,
        key: creds.private_key,
        scopes: ['https://www.googleapis.com/auth/spreadsheets'],
    });

    const doc = new GoogleSpreadsheet(VENTAS_SHEET_ID, serviceAccountAuth);

    try {
        console.log(`Attempting to load doc: ${VENTAS_SHEET_ID}`);
        await doc.loadInfo();
        console.log(`SUCCESS! Access confirmed.`);
        console.log(`Title: ${doc.title}`);
        console.log(`Sheet Count: ${doc.sheetCount}`);
        doc.sheetsByIndex.forEach(sheet => {
            console.log(` - Sheet: ${sheet.title} (ID: ${sheet.sheetId})`);
        });
    } catch (error) {
        console.error('ERROR: Could not access the sheet.');
        console.error(error.message);
    }
}

testAccess();
