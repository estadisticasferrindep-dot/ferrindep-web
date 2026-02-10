const fs = require('fs');
const axios = require('axios');
const { GoogleSpreadsheet } = require('google-spreadsheet');
const { JWT } = require('google-auth-library');
require('dotenv').config();

const FLEX_COSTS = { 'CABA': 3500, 'GBA_1': 4500, 'GBA_2': 5500, 'GBA_3': 7000, 'ZONA_1': 5000 };
const CITY_MAPPING = {
    'CAPITAL FEDERAL': 'CABA', 'BUENOS AIRES': 'CABA', 'CABA': 'CABA',
    'SAN MARTIN': 'GBA_1', 'TRES DE FEBRERO': 'GBA_1', 'VICENTE LOPEZ': 'GBA_1',
    'SAN ISIDRO': 'GBA_1', 'TIGRE': 'GBA_1', 'MORON': 'GBA_1', 'ITUZAINGO': 'GBA_1',
    'HURLINGHAM': 'GBA_1', 'LA MATANZA': 'GBA_1', 'SAN FERNANDO': 'GBA_1',
    'LOMAS DE ZAMORA': 'GBA_1', 'LANUS': 'GBA_1', 'AVELLANEDA': 'GBA_1',
    'ESCOBAR': 'GBA_2', 'PILAR': 'GBA_3', 'MORENO': 'GBA_2', 'MERLO': 'GBA_2',
    'EZEIZA': 'GBA_2', 'QUILMES': 'GBA_2', 'ZARATE': 'GBA_3', 'CAMPANA': 'GBA_3'
};

const ALL_TABS = ['10x10mm', '15x15mm', '20x20mm', '25x25mm', '40x40mm', '50x50mm', '50x150mm', 'Mosquitero', 'Cerco', 'Rombos'];

async function analyzeDailyMargin() {
    const mlToken = fs.readFileSync('ml_token.txt', 'utf8').trim();

    // Feb 8, 2026 in UTC-3
    const dateFrom = '2026-02-08T00:00:00.000-03:00';
    const dateTo = '2026-02-08T23:59:59.999-03:00';

    console.log(`=== ANÁLISIS DE MARGEN - 8 de Febrero 2026 ===\n`);

    // 1. Fetch orders for Feb 8
    let allOrders = [];
    let offset = 0;
    while (true) {
        const url = `https://api.mercadolibre.com/orders/search?seller=97128565&order.status=paid&order.date_created.from=${encodeURIComponent(dateFrom)}&order.date_created.to=${encodeURIComponent(dateTo)}&limit=50&offset=${offset}`;
        const res = await axios.get(url, { headers: { Authorization: `Bearer ${mlToken}` } });
        const results = res.data.results || [];
        allOrders = allOrders.concat(results);
        if (results.length < 50) break;
        offset += 50;
    }

    console.log(`Órdenes encontradas: ${allOrders.length}\n`);
    if (allOrders.length === 0) { console.log('No hay órdenes para esta fecha.'); return; }

    // 2. Load Google Sheets cost data
    const creds = require('./credentials.json');
    const auth = new JWT({ email: creds.client_email, key: creds.private_key, scopes: ['https://www.googleapis.com/auth/spreadsheets'] });
    const doc = new GoogleSpreadsheet(process.env.SHEET_ID, auth);
    await doc.loadInfo();

    // Preload all SKU costs from all tabs
    const skuCosts = {};
    for (const tabName of ALL_TABS) {
        const sheet = doc.sheetsByTitle[tabName];
        if (!sheet) continue;
        try {
            await sheet.loadCells('A1:C600');
            for (let r = 0; r < 600; r++) {
                const skuCell = sheet.getCell(r, 0);
                const costCell = sheet.getCell(r, 2);
                const skuVal = String(skuCell.value || '').trim();
                if (skuVal && skuVal !== 'null') {
                    let cost = 0;
                    if (typeof costCell.value === 'number') {
                        cost = costCell.value;
                    } else {
                        const raw = costCell.formattedValue || String(costCell.value || '');
                        cost = parseFloat(raw.replace(/\$/g, '').replace(/\./g, '').replace(/,/g, '.').trim()) || 0;
                    }
                    if (cost > 0) skuCosts[skuVal] = cost;
                }
            }
        } catch (e) { /* skip tab errors */ }
    }
    console.log(`SKUs cargados de Google Sheets: ${Object.keys(skuCosts).length}\n`);

    // 3. Analyze each order
    let totalRevAll = 0, totalFeeAll = 0, totalShipAll = 0, totalTaxAll = 0;
    let totalLogAll = 0, totalAdvAll = 0, totalCostAll = 0, totalProfitAll = 0;
    let orderResults = [];

    for (const order of allOrders) {
        try {
            const oi = order.order_items[0];
            const title = oi.item.title;
            const sku = oi.item.seller_sku;
            const qty = oi.quantity;
            const unitPrice = oi.unit_price;
            const revenue = unitPrice * qty;
            const fee = oi.sale_fee * qty;

            // Shipping
            let shipCost = 0, shipMode = 'Unknown', logCost = 0, city = '';
            if (order.shipping && order.shipping.id) {
                try {
                    const shipRes = await axios.get(`https://api.mercadolibre.com/shipments/${order.shipping.id}`, {
                        headers: { Authorization: `Bearer ${mlToken}` }
                    });
                    shipMode = shipRes.data.logistic_type;
                    if (shipRes.data.receiver_address) city = (shipRes.data.receiver_address.city.name || '').toUpperCase();
                    if (shipRes.data.shipping_option && shipRes.data.shipping_option.list_cost) {
                        shipCost = shipRes.data.shipping_option.list_cost;
                    }
                    if (shipMode === 'self_service') {
                        let zone = CITY_MAPPING[city];
                        if (!zone) { for (const k in CITY_MAPPING) { if (city.includes(k)) { zone = CITY_MAPPING[k]; break; } } }
                        if (!zone) zone = 'ZONA_1';
                        logCost = FLEX_COSTS[zone] || 5000;
                    }
                } catch (e) { }
            }

            // Tax detection via payment
            let taxAmount = 0;
            let totalNet = 0;
            if (order.payments) {
                for (const p of order.payments) {
                    if (p.status === 'approved' || p.status === 'accredited') {
                        try {
                            const payRes = await axios.get(`https://api.mercadolibre.com/collections/${p.id}`, {
                                headers: { Authorization: `Bearer ${mlToken}` }
                            });
                            if (payRes.data.net_received_amount) totalNet += payRes.data.net_received_amount;
                        } catch (e) { }
                    }
                }
            }
            const theoNet = revenue - fee - shipCost;
            if (totalNet > 0) {
                const gap = theoNet - totalNet;
                if (gap > 1.0) taxAmount = gap;
            }

            // Cost lookup
            const unitCost = skuCosts[String(sku)] || 0;
            const totalCost = unitCost * qty;

            // Margin calc
            const netBeforeAdv = revenue - fee - shipCost - taxAmount - logCost;
            const advCost = netBeforeAdv * 0.046;
            const netInHand = netBeforeAdv - advCost;
            const profit = netInHand - totalCost;
            const margin = revenue > 0 ? (profit / revenue) * 100 : 0;

            orderResults.push({
                orderId: order.id, title, sku, qty, revenue, fee, shipCost, logCost, taxAmount, advCost, totalCost, netInHand, profit, margin, shipMode, city, unitCost
            });

            totalRevAll += revenue;
            totalFeeAll += fee;
            totalShipAll += shipCost;
            totalTaxAll += taxAmount;
            totalLogAll += logCost;
            totalAdvAll += advCost;
            totalCostAll += totalCost;
            totalProfitAll += profit;

        } catch (e) {
            const safeMsg = e.message || String(e);
            console.error(`Error en orden ${order.id}: ${safeMsg.substring(0, 200)}`);
        }
    }

    // 4. Print results
    console.log(`| # | SKU | Producto | Cant | Ingreso | Comisión | Envío | Impuesto | Costo | Ganancia | Margen |`);
    console.log(`|---|-----|----------|------|---------|----------|-------|----------|-------|----------|--------|`);

    for (let i = 0; i < orderResults.length; i++) {
        const o = orderResults[i];
        const shortTitle = o.title.substring(0, 35);
        console.log(`| ${i + 1} | ${o.sku || 'N/A'} | ${shortTitle} | ${o.qty} | $${o.revenue.toFixed(0)} | $${o.fee.toFixed(0)} | $${o.shipCost.toFixed(0)} | $${o.taxAmount.toFixed(0)} | $${o.totalCost.toFixed(0)} | $${o.profit.toFixed(0)} | ${o.margin.toFixed(1)}% |`);
    }

    // Summary
    const totalMargin = totalRevAll > 0 ? (totalProfitAll / totalRevAll) * 100 : 0;
    console.log(`\n=== RESUMEN DEL DÍA ===`);
    console.log(`Total Órdenes:     ${allOrders.length}`);
    console.log(`Total Ingreso:     $${totalRevAll.toFixed(2)}`);
    console.log(`Total Comisión ML: -$${totalFeeAll.toFixed(2)}`);
    console.log(`Total Envío:       -$${totalShipAll.toFixed(2)}`);
    console.log(`Total Logística:   -$${totalLogAll.toFixed(2)}`);
    console.log(`Total Impuestos:   -$${totalTaxAll.toFixed(2)}`);
    console.log(`Total Adelanto:    -$${totalAdvAll.toFixed(2)}`);
    console.log(`Total Costo Prod:  -$${totalCostAll.toFixed(2)}`);
    console.log(`========================`);
    console.log(`GANANCIA TOTAL:    $${totalProfitAll.toFixed(2)}`);
    console.log(`MARGEN PROMEDIO:   ${totalMargin.toFixed(2)}%`);

    // Flag orders with issues
    const noSku = orderResults.filter(o => !o.sku);
    const noCost = orderResults.filter(o => o.unitCost === 0 && o.sku);
    const lowMargin = orderResults.filter(o => o.margin < 25 && o.unitCost > 0);

    if (noSku.length > 0) console.log(`\n⚠️ ${noSku.length} órdenes sin SKU`);
    if (noCost.length > 0) console.log(`⚠️ ${noCost.length} órdenes sin costo (SKU no encontrado en Sheet)`);
    if (lowMargin.length > 0) {
        console.log(`\n🔴 ${lowMargin.length} órdenes con margen < 25%:`);
        lowMargin.forEach(o => console.log(`   SKU ${o.sku}: ${o.margin.toFixed(1)}% ($${o.profit.toFixed(0)} ganancia)`));
    }
}

analyzeDailyMargin();
