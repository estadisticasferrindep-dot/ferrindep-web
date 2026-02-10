const { GoogleSpreadsheet } = require('google-spreadsheet');
const { JWT } = require('google-auth-library');
const axios = require('axios');
require('dotenv').config();

const SHEET_TAB_NAME = '50x50mm';
const SOURCE_ROW_INDEX = 84; // Row 85 in GUI
const DEST_ROW_INDEX = 85;   // Row 86 in GUI (The new empty one)
const END_COL_INDEX = 25;    // Column Y (Index 24) -> EndIndex 25 (Exclusive)

async function copyRow() {
    try {
        console.log(`Copying Row ${SOURCE_ROW_INDEX + 1} to Row ${DEST_ROW_INDEX + 1} (Cols A-Y)...`);

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
        const token = await serviceAccountAuth.getAccessToken();

        const res = await axios.post(
            `https://sheets.googleapis.com/v4/spreadsheets/${process.env.SHEET_ID}:batchUpdate`,
            {
                requests: [
                    {
                        copyPaste: {
                            source: {
                                sheetId: sheetId,
                                startRowIndex: SOURCE_ROW_INDEX,
                                endRowIndex: SOURCE_ROW_INDEX + 1,
                                startColumnIndex: 0,
                                endColumnIndex: END_COL_INDEX
                            },
                            destination: {
                                sheetId: sheetId,
                                startRowIndex: DEST_ROW_INDEX,
                                endRowIndex: DEST_ROW_INDEX + 1,
                                startColumnIndex: 0,
                                endColumnIndex: END_COL_INDEX
                            },
                            pasteType: "PASTE_NORMAL", // Copies Values, Formulas, Formatting
                            pasteOrientation: "NORMAL"
                        }
                    }
                ]
            },
            {
                headers: { Authorization: `Bearer ${token.token || token}` }
            }
        );

        console.log('✅ Copy Operation Successful!');
        console.log('Response:', res.status, res.statusText);

    } catch (e) {
        console.error('Error:', e.message);
        if (e.response) console.error('API Error:', JSON.stringify(e.response.data, null, 2));
    }
}

copyRow();
