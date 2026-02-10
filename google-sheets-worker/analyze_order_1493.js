const fs = require('fs');
const axios = require('axios');
const { GoogleSpreadsheet } = require('google-spreadsheet');
const { JWT } = require('google-auth-library');
require('dotenv').config();

async function run() {
    const mlToken = fs.readFileSync('ml_token.txt', 'utf8').trim();
    const ID = '2000011472801493';

    // Try as order
    console.log(`--- Trying as ORDER ---`);
    try {
        const r = await axios.get(`https://api.mercadolibre.com/orders/${ID}`, { headers: { Authorization: `Bearer ${mlToken}` } });
        console.log(`Found! Status: ${r.data.status}`);
    } catch (e) { console.log(`${e.response ? e.response.status : e.message}: ${e.response ? e.response.data.message : ''}`); }

    // Try as pack
    console.log(`--- Trying as PACK ---`);
    try {
        const r = await axios.get(`https://api.mercadolibre.com/packs/${ID}`, { headers: { Authorization: `Bearer ${mlToken}` } });
        console.log(`Found! Orders: ${JSON.stringify(r.data.orders)}`);
    } catch (e) { console.log(`${e.response ? e.response.status : e.message}: ${e.response ? e.response.data.message : ''}`); }

    // Try as shipment
    console.log(`--- Trying as SHIPMENT ---`);
    try {
        const r = await axios.get(`https://api.mercadolibre.com/shipments/${ID}`, { headers: { Authorization: `Bearer ${mlToken}` } });
        console.log(`Found! Status: ${r.data.status}, Order: ${r.data.order_id}`);
    } catch (e) { console.log(`${e.response ? e.response.status : e.message}: ${e.response ? e.response.data.message : ''}`); }

    // Now read BOTH known sheets for "25x25mm" tab, row 67
    const creds = require('./credentials.json');
    const auth = new JWT({ email: creds.client_email, key: creds.private_key, scopes: ['https://www.googleapis.com/auth/spreadsheets'] });

    const SHEETS = {
        'PRICING': process.env.SHEET_ID_PRICING,
        'STOCK': process.env.SHEET_ID_STOCK
    };

    for (const [name, sid] of Object.entries(SHEETS)) {
        console.log(`\n--- Sheet: ${name} ---`);
        try {
            const doc = new GoogleSpreadsheet(sid, auth);
            await doc.loadInfo();
            console.log(`Title: ${doc.title}`);

            const sheet = doc.sheetsByTitle['25x25mm'];
            if (!sheet) { console.log('No "25x25mm" tab'); continue; }

            await sheet.loadCells('A1:U70');

            // Headers
            let headers = [];
            for (let c = 0; c < 21; c++) headers.push(sheet.getCell(0, c).value || `Col${c}`);

            // Row 67 (0-indexed = 66)
            console.log(`\nRow 67:`);
            for (let c = 0; c < 21; c++) {
                const cell = sheet.getCell(66, c);
                if (cell.value !== null && cell.value !== '') {
                    console.log(`  Col ${c} (${headers[c]}): ${cell.formattedValue || cell.value}`);
                }
            }
        } catch (e) { console.log(`Error: ${e.message}`); }
    }
}

run();
