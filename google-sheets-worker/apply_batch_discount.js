const { GoogleSpreadsheet } = require('google-spreadsheet');
const { JWT } = require('google-auth-library');
const axios = require('axios');
const fs = require('fs');
require('dotenv').config();
const creds = require('./credentials.json');

const TARGET_SKUS = ['2945', '2950', '2905', '2895'];
const DISCOUNT_PCT = 0.05;
const DURATION_DAYS = 3;

async function applyBatchDiscounts() {
    const serviceAccountAuth = new JWT({
        email: creds.client_email,
        key: creds.private_key,
        scopes: ['https://www.googleapis.com/auth/spreadsheets'],
    });

    const doc = new GoogleSpreadsheet(process.env.SHEET_ID, serviceAccountAuth);
    await doc.loadInfo();

    // Tabs to search
    const TABS = ['40x40mm', '20x20mm', '50x50mm', '10x10mm', '15x15mm', 'Mosquitero'];
    const mlToken = fs.readFileSync('ml_token.txt', 'utf8').trim();

    // Dates
    const now = new Date();
    const endDate = new Date();
    endDate.setDate(now.getDate() + DURATION_DAYS);
    const startDateStr = now.toISOString().split('.')[0];
    const endDateStr = endDate.toISOString().split('.')[0];

    console.log(`Starting Batch Discount for ${TARGET_SKUS.join(', ')}...`);

    for (const sku of TARGET_SKUS) {
        let itemId = null;
        let foundTab = null;

        // 1. Find Item ID in Sheets
        for (const tabName of TABS) {
            const sheet = doc.sheetsByTitle[tabName];
            if (!sheet) continue;

            // Checking first 200 rows usually enough?
            await sheet.loadCells('A1:S200');
            // Loop
            for (let r = 0; r < 200; r++) {
                const cellSku = sheet.getCell(r, 0).value;
                if (String(cellSku).trim() === String(sku).trim()) {
                    itemId = sheet.getCell(r, 18).value; // Col S for ID
                    foundTab = tabName;
                    break;
                }
            }
            if (itemId) break;
        }

        if (!itemId) {
            console.log(`[WARN] SKU ${sku} not found in sheets.`);
            continue;
        }

        // Clean ID
        itemId = String(itemId).replace('#', '').trim();
        if (!itemId.startsWith('MLA')) itemId = 'MLA' + itemId;

        console.log(`\n--- Processing SKU ${sku} (${foundTab}) ---`);
        console.log(`Item ID: ${itemId}`);

        // 2. Fetch Current Price from ML to be safe
        let currentPrice = 0;
        try {
            const itemRes = await axios.get(`https://api.mercadolibre.com/items/${itemId}`, {
                headers: { Authorization: `Bearer ${mlToken}` }
            });
            currentPrice = itemRes.data.price;
            console.log(`Current Price: $${currentPrice}`);
        } catch (e) {
            console.log(`[ERROR] Failed to fetch item ${itemId}: ${e.message}`);
            continue;
        }

        // 3. Create Discount
        const dealPrice = Math.floor(currentPrice * (1 - DISCOUNT_PCT));
        const payload = {
            promotion_type: 'PRICE_DISCOUNT',
            deal_price: dealPrice,
            start_date: startDateStr,
            finish_date: endDateStr,
            original_price: currentPrice,
            name: `Descuento 5% SKU ${sku}`
        };

        try {
            const res = await axios.post(`https://api.mercadolibre.com/seller-promotions/items/${itemId}?app_version=v2`, payload, {
                headers: { Authorization: `Bearer ${mlToken}` }
            });
            console.log(`[SUCCESS] Discount Applied! New Price: $${dealPrice}`);
        } catch (e) {
            console.error(`[FAIL] Could not apply discount: ${e.message}`);
            if (e.response) console.log(JSON.stringify(e.response.data.message || e.response.data, null, 2));
        }
    }
}

applyBatchDiscounts();
