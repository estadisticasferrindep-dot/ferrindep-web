const fs = require('fs');
const axios = require('axios');
const { GoogleSpreadsheet } = require('google-spreadsheet');
const { JWT } = require('google-auth-library');
require('dotenv').config();

const ORDER_ID = '2000015046917810'; // The Postal Order with Hidden Taxes
const SKU_TO_FIND = '';
const SHEET_TAB_NAME = '';

// --- CONFIGURATION FLEX ---
const FLEX_COSTS = {
    'ZONA_1': 5000,
    'GBA_1': 6000,
    'GBA_2': 7000,
    'GBA_3': 9000
};

const CITY_MAPPING = {
    'CAPITAL FEDERAL': 'ZONA_1', 'CABA': 'ZONA_1', 'CIUDAD AUTONOMA DE BUENOS AIRES': 'ZONA_1',
    'VICENTE LOPEZ': 'ZONA_1', 'SAN ISIDRO': 'ZONA_1', 'SAN MARTIN': 'ZONA_1', '3 DE FEBRERO': 'ZONA_1',
    'TIGRE': 'GBA_1', 'MALVINAS ARGENTINAS': 'GBA_1', 'HURLINGHAM': 'GBA_1', 'ITUZAINGO': 'GBA_1',
    'LOMAS DE ZAMORA': 'GBA_1', 'LANUS': 'GBA_1', 'AVELLANEDA': 'GBA_1',
    'ESCOBAR': 'GBA_2', 'PILAR': 'GBA_3', 'MORENO': 'GBA_2', 'MERLO': 'GBA_2', 'EZEIZA': 'GBA_2', 'QUILMES': 'GBA_2',
    'ZARATE': 'GBA_3', 'CAMPANA': 'GBA_3'
};

async function analyzeMargin() {
    try {
        console.log(`Analyzing Order #${ORDER_ID}...`);

        // --- 1. GET ML ORDER DATA ---
        const mlToken = fs.readFileSync('ml_token.txt', 'utf8').trim();
        const orderRes = await axios.get(`https://api.mercadolibre.com/orders/${ORDER_ID}`, {
            headers: { Authorization: `Bearer ${mlToken}` }
        });
        const order = orderRes.data;

        // Sale Details
        const orderItem = order.order_items[0];
        const title = orderItem.item.title;
        const sku = orderItem.item.seller_sku;
        const quantity = orderItem.quantity;
        const saleFee = orderItem.sale_fee * quantity;
        const totalRevenue = orderItem.unit_price * quantity; // Base Revenue

        // Shipping & Flex Logic
        let shippingCost = 0;
        let shippingMode = 'Unknown';
        let logisticsCost = 0;
        let receiverCity = 'Unknown';

        if (order.shipping && order.shipping.id) {
            try {
                const shipRes = await axios.get(`https://api.mercadolibre.com/shipments/${order.shipping.id}`, {
                    headers: { Authorization: `Bearer ${mlToken}` }
                });
                const shipment = shipRes.data;
                shippingMode = shipment.logistic_type;

                if (shipment.receiver_address) {
                    receiverCity = (shipment.receiver_address.city.name || '').toUpperCase();
                }

                if (shipment.shipping_option && shipment.shipping_option.list_cost) {
                    shippingCost = shipment.shipping_option.list_cost;
                }

                // FLEX LOGIC
                if (shippingMode === 'self_service') {
                    let zone = CITY_MAPPING[receiverCity];
                    if (!zone) {
                        for (const key in CITY_MAPPING) {
                            if (receiverCity.includes(key)) { zone = CITY_MAPPING[key]; break; }
                        }
                    }
                    if (!zone) zone = 'ZONA_1';
                    logisticsCost = FLEX_COSTS[zone] || 5000;
                }
            } catch (err) { console.log('  [Warn] Could not fetch shipment details.'); }
        }

        // Taxes & Net Audit
        let taxAmount = 0; // Default
        if (order.taxes && order.taxes.amount) taxAmount = order.taxes.amount;

        // Verify Real Net Received vs Theoretical
        let totalNetReceived = 0;
        if (order.payments && order.payments.length > 0) {
            for (const p of order.payments) {
                if (p.status === 'approved' || p.status === 'accredited') {
                    try {
                        const payRes = await axios.get(`https://api.mercadolibre.com/collections/${p.id}`, {
                            headers: { Authorization: `Bearer ${mlToken}` }
                        });
                        const payDetail = payRes.data;
                        if (payDetail.net_received_amount) {
                            totalNetReceived += payDetail.net_received_amount;
                        }
                    } catch (err) {/*Ignore*/ }
                }
            }
        }

        // Calculate Gap for Hidden Taxes
        // Theoretical Net (ML Payout) = Revenue - Fee - ShippingCost(Seller)
        const theoreticalNet = totalRevenue - saleFee - shippingCost;
        if (totalNetReceived > 0) {
            const gap = theoreticalNet - totalNetReceived;
            if (gap > 1.0) { // Tolerance
                console.log(`  [Tax Audit] Detected Hidden Tax/Retention: $${gap.toFixed(2)}`);
                taxAmount = gap;
            }
        }

        console.log(`\n--- Sale Data ---`);
        console.log(`Item: ${title}`);
        console.log(`SKU: ${sku}`);
        console.log(`Total Revenue: $${totalRevenue}`);
        console.log(`ML Fee: -$${saleFee}`);
        if (shippingMode === 'self_service') {
            console.log(`Shipping Mode: FLEX (Auto-Logística)`);
            console.log(`Ciudad: ${receiverCity}`);
            console.log(`Costo Logística: -$${logisticsCost}`);
            console.log(`Envío ML: -$${shippingCost}`);
        } else {
            console.log(`Shipping Mode: ${shippingMode}`);
            console.log(`Envío ML: -$${shippingCost}`);
        }
        console.log(`Impuestos (Detectados): -$${taxAmount}`);


        // --- SHEET Lookup ---
        let sheetTab = '50x50mm';
        if (title.includes('20x20')) sheetTab = '20x20mm';
        if (title.includes('15x15')) sheetTab = '15x15mm';
        if (title.includes('10x10')) sheetTab = '10x10mm';

        if (!sku) throw new Error("Item has no SKU!");

        const creds = require('./credentials.json');
        const serviceAccountAuth = new JWT({
            email: creds.client_email,
            key: creds.private_key,
            scopes: ['https://www.googleapis.com/auth/spreadsheets'],
        });

        const doc = new GoogleSpreadsheet(process.env.SHEET_ID, serviceAccountAuth);
        await doc.loadInfo();
        const sheet = doc.sheetsByTitle[sheetTab];
        if (!sheet) throw new Error(`Tab '${sheetTab}' not found.`);

        await sheet.loadCells('A1:C600');
        let foundRow = null;
        let unitCost = 0;
        const MAX_ROWS = 600;

        for (let r = 0; r < MAX_ROWS; r++) {
            const cellSku = sheet.getCell(r, 0);
            const rSku = cellSku.value;
            if (String(rSku).trim() === String(sku).trim()) {
                const cellCost = sheet.getCell(r, 2);
                const rawCost = cellCost.formattedValue || cellCost.value;
                unitCost = typeof cellCost.value === 'number' ? cellCost.value : 0;
                if (typeof rawCost === 'string') {
                    unitCost = parseFloat(rawCost.replace(/\$/g, '').replace(/\./g, '').replace(/,/g, '.').trim());
                }
                foundRow = r;
                break;
            }
        }
        if (foundRow === null) throw new Error(`SKU ${sku} not found.`);

        console.log(`Costo Reposición: $${unitCost}`);
        const totalProductCost = unitCost * quantity;

        // --- FINAL CALCULATION ---
        let netIncomeBeforeAdvance = 0;

        if (shippingMode === 'self_service') {
            // Net = Revenue - Fee - Shipping - Tax - Logistics
            netIncomeBeforeAdvance = totalRevenue - saleFee - shippingCost - taxAmount - logisticsCost;
        } else {
            // Net = Revenue - Fee - Shipping - Tax
            netIncomeBeforeAdvance = totalRevenue - saleFee - shippingCost - taxAmount;
        }

        const advanceCost = netIncomeBeforeAdvance * 0.046;
        const netInHand = netIncomeBeforeAdvance - advanceCost;
        const profit = netInHand - totalProductCost;
        const margin = (profit / totalRevenue) * 100;

        console.log(`\n--- FINAL ANALYSIS ---`);
        console.log(`(+) Total Ingreso: $${totalRevenue.toFixed(2)}`);
        console.log(`(-) Comisión ML:   -$${saleFee.toFixed(2)}`);
        if (shippingMode === 'self_service') {
            console.log(`(-) Logística:     -$${logisticsCost.toFixed(2)} (${receiverCity})`);
            console.log(`(-) ML Envío:      -$${shippingCost.toFixed(2)}`);
        } else {
            console.log(`(-) Envío ML:      -$${shippingCost.toFixed(2)}`);
        }
        console.log(`(-) Impuestos:     -$${taxAmount.toFixed(2)}`);
        console.log(`--------------------------------`);
        console.log(`(=) Neto Pre-Adel: $${netIncomeBeforeAdvance.toFixed(2)}`);
        console.log(`(-) Adelanto 4.6%: -$${advanceCost.toFixed(2)}`);
        console.log(`(=) Neto en Mano:  $${netInHand.toFixed(2)}`);
        console.log(`(-) Costo Reposic: -$${totalProductCost.toFixed(2)}`);
        console.log(`================================`);
        console.log(`(=) GANANCIA:      $${profit.toFixed(2)}`);
        console.log(`(%) MARGEN:        ${margin.toFixed(2)}%`);

    } catch (e) {
        console.error('Error:', e.message);
        if (e.response) console.error('API Data:', JSON.stringify(e.response.data, null, 2));
    }
}

analyzeMargin();
