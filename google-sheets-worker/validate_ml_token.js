require('dotenv').config();
const { GoogleSpreadsheet } = require('google-spreadsheet');
const { JWT } = require('google-auth-library');
const creds = require('./credentials.json');
const axios = require('axios');

const TOKEN_TAB_NAME = 'Tokens';

async function validateToken() {
    const jwt = new JWT({
        email: creds.client_email,
        key: creds.private_key,
        scopes: ['https://www.googleapis.com/auth/spreadsheets'],
    });

    const doc = new GoogleSpreadsheet(process.env.SHEET_ID, jwt);
    await doc.loadInfo();

    const sheet = doc.sheetsByTitle[TOKEN_TAB_NAME];
    await sheet.loadCells('B1'); // Cell with token (Row 1, Col 2 -> B1. Wait, previous output: Row 1: ['Token', 'APP...'])
    // Row 1 is index 0. Value at index 1 is 'APP...'. So Cell (0, 1).

    const accessToken = sheet.getCell(0, 1).value;

    if (!accessToken) {
        console.error('No token found in B1');
        return;
    }

    console.log(`Checking Token: ${accessToken.substring(0, 10)}...`);

    try {
        const res = await axios.get('https://api.mercadolibre.com/users/me', {
            headers: { Authorization: `Bearer ${accessToken}` }
        });
        console.log('Token Valid!');
        console.log(`User ID: ${res.data.id}`);
        console.log(`Nickname: ${res.data.nickname}`);
    } catch (e) {
        console.error('Token Invalid or Error:', e.response ? e.response.data : e.message);
    }
}

validateToken().catch(console.error);
