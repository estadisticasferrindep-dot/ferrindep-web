require('dotenv').config();
const { GoogleSpreadsheet } = require('google-spreadsheet');
const { JWT } = require('google-auth-library');
const creds = require('./credentials.json');
const axios = require('axios');

// ID provided by user: 1pBIByoZ1i6pUNSn7uw2nYMTxMNWQ7bka-btA85AHYY8
const STOCK_SHEET_ID = '1pBIByoZ1i6pUNSn7uw2nYMTxMNWQ7bka-btA85AHYY8';
const TOKEN_TAB_NAME = 'Tokens';

async function validateStockToken() {
    const jwt = new JWT({
        email: creds.client_email,
        key: creds.private_key,
        scopes: ['https://www.googleapis.com/auth/spreadsheets'],
    });

    const doc = new GoogleSpreadsheet(STOCK_SHEET_ID, jwt);
    await doc.loadInfo();
    console.log(`Connected to: ${doc.title}`);

    const sheet = doc.sheetsByTitle[TOKEN_TAB_NAME];
    if (!sheet) {
        console.error(`Tab '${TOKEN_TAB_NAME}' not found.`);
        return;
    }

    await sheet.loadCells('B1');

    // Row 1 (Index 0), Col B (Index 1)
    const accessToken = sheet.getCell(0, 1).value;

    if (!accessToken) {
        console.error('No token found in B1');
        return;
    }

    console.log(`Checking Token: ${accessToken.substring(0, 15)}...`);

    try {
        const res = await axios.get('https://api.mercadolibre.com/users/me', {
            headers: { Authorization: `Bearer ${accessToken}` }
        });
        console.log('Token Valid!');
        console.log(`User ID: ${res.data.id}`);
        console.log(`Nickname: ${res.data.nickname}`);

        // Save valid token to file for use by other scripts
        const fs = require('fs');
        fs.writeFileSync('ml_token.txt', accessToken);
        console.log('Token saved to ml_token.txt');

    } catch (e) {
        console.error('Token Invalid or Error:', e.response ? e.response.data : e.message);
    }
}

validateStockToken().catch(console.error);
