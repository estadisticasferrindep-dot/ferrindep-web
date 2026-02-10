const { GoogleSpreadsheet } = require('google-spreadsheet');
const { JWT } = require('google-auth-library');
const axios = require('axios');
require('dotenv').config();

const SHEET_TAB_NAME = '50x50mm';
const TARGET_ROW_INDEX_GUI = 85; // User says "below row 85"
// API Logic:
// Row 85 = Index 84.
// "Below 85" means we want the new row to be at Index 85 (becoming the new Row 86).
const INSERT_AT_INDEX = 85;

async function insertRow() {
    try {
        console.log(`Preparing to insert empty row at Index ${INSERT_AT_INDEX} (Row ${TARGET_ROW_INDEX_GUI + 1}) in '${SHEET_TAB_NAME}'...`);

        const creds = require('./credentials.json');
        const serviceAccountAuth = new JWT({
            email: creds.client_email,
            key: creds.private_key,
            scopes: ['https://www.googleapis.com/auth/spreadsheets'],
        });

        const doc = new GoogleSpreadsheet(process.env.SHEET_ID, serviceAccountAuth);
        await doc.loadInfo();

        const sheet = doc.sheetsByTitle[SHEET_TAB_NAME];
        if (!sheet) throw new Error(`Tab '${SHEET_TAB_NAME}' not found.`);

        const sheetId = sheet.sheetId;
        console.log(`Sheet ID: ${sheetId}`);

        // We use direct API call for insertDimension because google-spreadsheet might not expose precise insertion easily
        // Actually we can access the underlying axios instance from google-spreadsheet or just use the token we have?
        // google-spreadsheet doc.axios corresponds to the authenticated client? 
        // Let's rely on the serviceAccountAuth to get a token and verify, or use the doc.axios directly if available.
        // Actually, doc.axios is available in newer versions. Let's try to grab the token from auth.

        const token = await serviceAccountAuth.getAccessToken(); // returns object with token

        const res = await axios.post(
            `https://sheets.googleapis.com/v4/spreadsheets/${process.env.SHEET_ID}:batchUpdate`,
            {
                requests: [
                    {
                        insertDimension: {
                            range: {
                                sheetId: sheetId,
                                dimension: "ROWS",
                                startIndex: INSERT_AT_INDEX,
                                endIndex: INSERT_AT_INDEX + 1
                            },
                            inheritFromBefore: true // Optional: inherits formatting from row above
                        }
                    }
                ]
            },
            {
                headers: {
                    Authorization: `Bearer ${token.token || token}`
                    // .getAccessToken() returns { token, res } usually or just string? 
                    // GoogleAuthLibrary v? gets { token: '...' } usually.
                }
            }
        );

        console.log('✅ Row Inserted Successfully!');
        console.log('Response:', res.status, res.statusText);

    } catch (e) {
        console.error('Error:', e.message);
        if (e.response) console.error('API Error:', JSON.stringify(e.response.data, null, 2));
    }
}

insertRow();
