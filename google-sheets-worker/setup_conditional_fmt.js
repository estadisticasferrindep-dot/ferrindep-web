require('dotenv').config();
const { JWT } = require('google-auth-library');
const creds = require('./credentials.json');
const axios = require('axios'); // We need raw HTTP for this, google-spreadsheet doesn't expose addConditionalFormatRule easily?
// Actually, check if google-spreadsheet exposes it. Documentation says no direct method in v3-v4 basic usage.
// So we use batchUpdate via axios or google-apis.
// Using raw axios with JWT token is easiest.

async function setupFormatting() {
    const jwt = new JWT({
        email: creds.client_email,
        key: creds.private_key,
        scopes: ['https://www.googleapis.com/auth/spreadsheets'],
    });

    const tokens = await jwt.authorize();
    const accessToken = tokens.access_token;
    const spreadsheetId = process.env.SHEET_ID;

    // First need to get SheetId (GridId) for 'Hoja1'
    const metaUrl = `https://sheets.googleapis.com/v4/spreadsheets/${spreadsheetId}`;
    const metaRes = await axios.get(metaUrl, { headers: { Authorization: `Bearer ${accessToken}` } });

    const sheet = metaRes.data.sheets.find(s => s.properties.title === 'Hoja1'); // Check Title
    // Previous scripts used 'Hoja1' (no space). 'analyze_tabs.js' verified 'Hoja1'.
    if (!sheet) {
        console.error('Hoja1 not found in metadata.');
        return;
    }
    const sheetId = sheet.properties.sheetId;
    console.log(`Hoja1 Grid ID: ${sheetId}`);

    // Request body for addConditionalFormatRule
    // Rule: K2:K (StartRow 1, EndRow undefined? or Grid Limit)
    // Formula: =K2<=$N$1
    // Color: #EA4335 (Red) or #F4C7C3 (Light Red) background.

    // NOTE: Formula syntax via API expects US English (comma, no semicolon).
    // The previous error in cell formula was because we wrote to CELL VALUE property.
    // Conditional Formatting API expects standard syntax.

    const requests = [
        {
            addConditionalFormatRule: {
                rule: {
                    ranges: [
                        {
                            sheetId: sheetId,
                            startRowIndex: 1, // K2 (Skip Header K1)
                            // endRowIndex: omit to go to end
                            startColumnIndex: 10, // K
                            endColumnIndex: 11
                        }
                    ],
                    booleanRule: {
                        condition: {
                            type: 'CUSTOM_FORMULA',
                            values: [{ userEnteredValue: '=K2<=$N$1' }]
                        },
                        format: {
                            backgroundColor: { red: 0.95, green: 0.8, blue: 0.8 } // Light Red
                        }
                    }
                },
                index: 0 // Top priority
            }
        }
    ];

    const batchUrl = `https://sheets.googleapis.com/v4/spreadsheets/${spreadsheetId}:batchUpdate`;
    try {
        const res = await axios.post(batchUrl, { requests }, { headers: { Authorization: `Bearer ${accessToken}` } });
        console.log(`Conditional Formatting Rule Added. Replies: ${res.data.replies.length}`);
    } catch (e) {
        console.error('Error adding rule:', e.response ? e.response.data : e.message);
    }
}

setupFormatting().catch(console.error);
