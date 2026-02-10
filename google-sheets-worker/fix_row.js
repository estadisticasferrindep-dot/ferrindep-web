const { GoogleSpreadsheet } = require('google-spreadsheet');
const { JWT } = require('google-auth-library');
const axios = require('axios');
require('dotenv').config();

// We found that Index 84 is Empty, and Index 85 is the content (3380).
// User wants Row 85 (Index 84) and Row 86 (Index 85) to be duplicates.
// So we copy 85 -> 84.

const TAB_NAME = '50x50mm';
const SOURCE_IDX = 85;
const DEST_IDX = 84;

async function fixRow() {
    try {
        console.log(`Fixing: Copying Row ${SOURCE_IDX + 1} -> Row ${DEST_IDX + 1}...`);

        const creds = require('./credentials.json');
        const serviceAccountAuth = new JWT({
            email: creds.client_email,
            key: creds.private_key,
            scopes: ['https://www.googleapis.com/auth/spreadsheets'],
        });

        const doc = new GoogleSpreadsheet(process.env.SHEET_ID, serviceAccountAuth);
        await doc.loadInfo();
        const sheet = doc.sheetsByTitle[TAB_NAME];
        const tokenToken = (await serviceAccountAuth.getAccessToken()).token;

        const res = await axios.post(
            `https://sheets.googleapis.com/v4/spreadsheets/${process.env.SHEET_ID}:batchUpdate`,
            {
                requests: [
                    {
                        copyPaste: {
                            source: {
                                sheetId: sheet.sheetId,
                                startRowIndex: SOURCE_IDX,
                                endRowIndex: SOURCE_IDX + 1,
                                startColumnIndex: 0,
                                endColumnIndex: 25
                            },
                            destination: {
                                sheetId: sheet.sheetId,
                                startRowIndex: DEST_IDX,
                                endRowIndex: DEST_IDX + 1,
                                startColumnIndex: 0,
                                endColumnIndex: 25
                            },
                            pasteType: "PASTE_NORMAL"
                        }
                    }
                ]
            },
            { headers: { Authorization: `Bearer ${tokenToken}` } }
        );

        console.log(`Restored Row 85: Status ${res.status}`);

    } catch (e) {
        console.error(e.message);
        if (e.response) console.error(JSON.stringify(e.response.data, null, 2));
    }
}

fixRow();
