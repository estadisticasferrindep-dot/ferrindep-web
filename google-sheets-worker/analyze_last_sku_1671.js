const fs = require('fs');
const axios = require('axios');
const { GoogleSpreadsheet } = require('google-spreadsheet');
const { JWT } = require('google-auth-library');
require('dotenv').config();

const ORDER_ID = '2000015076489098';

async function analyzeMargin() {
    const mlToken = fs.readFileSync('ml_token.txt', 'utf8').trim();

    // 1. Get Order
    const orderRes = await axios.get(`https://api.mercadolibre.com/orders/${ORDER_ID}`, {
        headers: { Authorization: `Bearer ${mlToken}` }
    });
    const order = orderRes.data;

    console.log(`=== ANÁLISIS ORDEN: ${order.id} ===`);
    console.log(`Fecha: ${order.date_created}`);
    const oi = order.order_items[0];
    console.log(`Producto: ${oi.item.title}`);
    console.log(`SKU: ${oi.item.seller_sku}`);
    console.log(`Cant: ${oi.quantity}`);
    console.log(`Precio Unit: $${oi.unit_price}`);

    // 2. Shipping
    let shipCost = 0;
    let shipMode = 'N/A';
    if (order.shipping && order.shipping.id) {
        console.log(`\nShipping ID: ${order.shipping.id}`);
        // Check if user meant this ID 1493?
        if (String(order.shipping.id).endsWith('1493')) console.log(`*** MATCH: Shipping ID ends in 1493! ***`);

        const shipRes = await axios.get(`https://api.mercadolibre.com/shipments/${order.shipping.id}`, {
            headers: { Authorization: `Bearer ${mlToken}` }
        });
        const sh = shipRes.data;
        shipMode = sh.logistic_type;
        console.log(`Modo: ${shipMode}`);
        if (sh.shipping_option && sh.shipping_option.list_cost) {
            shipCost = sh.shipping_option.list_cost;
            console.log(`Costo Envío ML: $${shipCost}`);
        }
    }

    // 3. Tax / Net
    let taxAmount = 0;
    let totalNet = 0;
    if (order.payments) {
        for (const p of order.payments) {
            if (p.status === 'approved' || p.status === 'accredited') {
                try {
                    const pr = await axios.get(`https://api.mercadolibre.com/collections/${p.id}`, { headers: { Authorization: `Bearer ${mlToken}` } });
                    totalNet += pr.data.net_received_amount;
                } catch (e) { }
            }
        }
    }
    const revenue = oi.unit_price * oi.quantity;
    const fee = oi.sale_fee * oi.quantity;
    const theoNet = revenue - fee - shipCost;
    if (totalNet > 0 && (theoNet - totalNet) > 1) {
        taxAmount = theoNet - totalNet;
        console.log(`Impuestos (IIBB): $${taxAmount.toFixed(2)}`);
    }

    // 4. Cost from Sheet
    const creds = require('./credentials.json');
    const auth = new JWT({ email: creds.client_email, key: creds.private_key, scopes: ['https://www.googleapis.com/auth/spreadsheets'] });
    const doc = new GoogleSpreadsheet(process.env.SHEET_ID, auth);
    await doc.loadInfo();

    // Look in tabs
    let unitCost = 0;
    const tabs = ['10x10mm', '15x15mm', '20x20mm', '25x25mm', '40x40mm', '50x50mm', '50x150mm', 'Mosquitero', 'Cerco', 'Rombos'];
    for (const t of tabs) {
        const sheet = doc.sheetsByTitle[t];
        if (!sheet) continue;
        await sheet.loadCells('A1:C300');
        for (let r = 0; r < 300; r++) {
            if (String(sheet.getCell(r, 0).value).trim() === String(oi.item.seller_sku).trim()) {
                const c = sheet.getCell(r, 2);
                if (typeof c.value === 'number') unitCost = c.value;
                else if (typeof c.formattedValue === 'string') {
                    unitCost = parseFloat(c.formattedValue.replace(/\$/g, '').replace(/\./g, '').replace(/,/g, '.').trim());
                }
                console.log(`\nCosto encontrado en '${t}': $${unitCost}`);
                break;
            }
        }
        if (unitCost > 0) break;
    }

    // 5. Calculate Margins
    console.log(`\n=== CÁLCULO MARGEN ===`);
    console.log(`(1) SIN descontar envío (Tu método)`);
    // Net = Rev - Fee - Tax - Adelanto4.6% - Cost
    // But sheet usually ignores shipping in the net calc for margin? 
    // Sheet Logic: Rev - Fee - IIBB - Adelanto - Cost
    const netSheet = revenue - fee - taxAmount;
    const advSheet = netSheet * 0.05; // User uses 5%
    const profitSheet = netSheet - advSheet - (unitCost * oi.quantity);
    const marginSheet = (profitSheet / revenue) * 100;

    console.log(`   Ganancia: $${profitSheet.toFixed(2)}`);
    console.log(`   Margen:   ${marginSheet.toFixed(2)}%`);

    console.log(`\n(2) DESCONTANDO envío (Mi método)`);
    // Net = Rev - Fee - Ship - Tax - Adelanto4.6% - Cost
    const netReal = revenue - fee - shipCost - taxAmount;
    const advReal = netReal * 0.046;
    const profitReal = netReal - advReal - (unitCost * oi.quantity);
    const marginReal = (profitReal / revenue) * 100;

    console.log(`   Ganancia: $${profitReal.toFixed(2)}`);
    console.log(`   Margen:   ${marginReal.toFixed(2)}%`);
}

analyzeMargin();
