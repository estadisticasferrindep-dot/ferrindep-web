const { GoogleSpreadsheet } = require('google-spreadsheet');
const { JWT } = require('google-auth-library');
const axios = require('axios');
const util = require('util');
require('dotenv').config();

const SHEET_TAB_NAME = '50x50mm';
const INSERT_AT_INDEX = 85;

async function insertRowV2() {
    try {
        console.log(`[V2] Inserting at Index ${INSERT_AT_INDEX}...`);

        const creds = require('./credentials.json');
        const serviceAccountAuth = new JWT({
            email: creds.client_email,
            key: creds.private_key,
            scopes: ['https://www.googleapis.com/auth/spreadsheets'],
        });

        const doc = new GoogleSpreadsheet(process.env.SHEET_ID, serviceAccountAuth);
        await doc.loadInfo();
        const sheet = doc.sheetsByTitle[SHEET_TAB_NAME];

        console.log(`Sheet ID (GID): ${sheet.sheetId}`);
        console.log(`Old Row Count: ${sheet.rowCount}`);

        // Token Debug
        const tokenObj = await serviceAccountAuth.getAccessToken();
        const tokenStr = tokenObj.token || tokenObj;
        console.log(`Token acquired (length ${tokenStr.length})...`);

        const payload = {
            requests: [
                {
                    insertDimension: {
                        range: {
                            sheetId: sheet.sheetId, // Use integer
                            dimension: "ROWS",
                            startIndex: INSERT_AT_INDEX,
                            endIndex: INSERT_AT_INDEX + 1
                        },
                        inheritFromBefore: false // CHANGED: Set to false to see if it helps
                    }
                }
            ]
        };

        console.log('Payload:', JSON.stringify(payload, null, 2));

        const res = await axios.post(
            `https://sheets.googleapis.com/v4/spreadsheets/${process.env.SHEET_ID}:batchUpdate`,
            payload,
            {
                headers: { Authorization: `Bearer ${tokenStr}` }
            }
        );

        console.log(`Response Status: ${res.status}`);

        // Immediate Verify
        await new Promise(r => setTimeout(r, 2000)); // Wait for propagation

        // Reload Doc to check count
        await doc.loadInfo(); // Reload manifest
        const newSheet = doc.sheetsByTitle[SHEET_TAB_NAME];
        console.log(`New Row Count: ${newSheet.rowCount}`);

        if (newSheet.rowCount > sheet.rowCount) {
            console.log('✅ SUCCESS: Row Count Increased.');
        } else {
            console.log('❌ FAIL: Row Count Unchanged.');
        }

    } catch (e) {
        console.error('Error:', e.message);
        if (e.response) console.error('API Error:', JSON.stringify(e.response.data, null, 2));
    }
}

insertRowV2();
