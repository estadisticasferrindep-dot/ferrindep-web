const fs = require('fs');
const axios = require('axios');
const { GoogleSpreadsheet } = require('google-spreadsheet');
const { JWT } = require('google-auth-library');
require('dotenv').config();

async function debugSku8700() {
    const mlToken = fs.readFileSync('ml_token.txt', 'utf8').trim();

    // 1. Find the order for SKU 8700 on Feb 8
    const dateFrom = '2026-02-08T00:00:00.000-03:00';
    const dateTo = '2026-02-08T23:59:59.999-03:00';

    const url = `https://api.mercadolibre.com/orders/search?seller=97128565&order.status=paid&order.date_created.from=${encodeURIComponent(dateFrom)}&order.date_created.to=${encodeURIComponent(dateTo)}&limit=50&offset=0`;
    const res = await axios.get(url, { headers: { Authorization: `Bearer ${mlToken}` } });
    const orders = res.data.results || [];

    const order = orders.find(o => o.order_items[0].item.seller_sku === '8700');
    if (!order) { console.log('Order not found!'); return; }

    console.log(`=== DEBUG ORDEN SKU 8700 ===`);
    console.log(`Order ID: ${order.id}`);
    console.log(`Date: ${order.date_created}`);

    const oi = order.order_items[0];
    console.log(`\n--- ITEM ---`);
    console.log(`Title: ${oi.item.title}`);
    console.log(`SKU: ${oi.item.seller_sku}`);
    console.log(`Item ID: ${oi.item.id}`);
    console.log(`Quantity: ${oi.quantity}`);
    console.log(`Unit Price: $${oi.unit_price}`);
    console.log(`Sale Fee (per unit): $${oi.sale_fee}`);
    console.log(`Total Revenue: $${oi.unit_price * oi.quantity}`);
    console.log(`Total Fee: $${oi.sale_fee * oi.quantity}`);

    // 2. Shipping details
    console.log(`\n--- SHIPPING ---`);
    console.log(`Shipping ID: ${order.shipping ? order.shipping.id : 'N/A'}`);

    let shipCost = 0, shipMode = 'N/A', logCost = 0, city = 'N/A';
    if (order.shipping && order.shipping.id) {
        const shipRes = await axios.get(`https://api.mercadolibre.com/shipments/${order.shipping.id}`, {
            headers: { Authorization: `Bearer ${mlToken}` }
        });
        const s = shipRes.data;
        shipMode = s.logistic_type;
        console.log(`Logistic Type: ${s.logistic_type}`);
        console.log(`Status: ${s.status}`);
        console.log(`Substatus: ${s.substatus}`);

        if (s.receiver_address) {
            city = (s.receiver_address.city.name || '').toUpperCase();
            console.log(`City: ${city}`);
            console.log(`State: ${s.receiver_address.state ? s.receiver_address.state.name : 'N/A'}`);
        }

        if (s.shipping_option) {
            console.log(`Shipping Option Name: ${s.shipping_option.name}`);
            console.log(`List Cost: $${s.shipping_option.list_cost}`);
            console.log(`Cost: $${s.shipping_option.cost}`);
            shipCost = s.shipping_option.list_cost || 0;
        }

        // Check if it's Flex
        if (shipMode === 'self_service') {
            console.log(`\n⚠️ FLEX DETECTED - Adding logistics cost`);
            logCost = 5000; // Default estimate
            console.log(`Estimated Flex Logistics: $${logCost}`);
        }

        // Dump cost_components if available
        if (s.cost_components) {
            console.log(`\nCost Components:`);
            console.log(JSON.stringify(s.cost_components, null, 2));
        }
    }

    // 3. Payments & Tax
    console.log(`\n--- PAYMENTS ---`);
    let totalNet = 0;
    if (order.payments) {
        for (const p of order.payments) {
            console.log(`Payment ID: ${p.id} | Status: ${p.status} | Amount: $${p.total_paid_amount}`);
            if (p.status === 'approved' || p.status === 'accredited') {
                try {
                    const payRes = await axios.get(`https://api.mercadolibre.com/collections/${p.id}`, {
                        headers: { Authorization: `Bearer ${mlToken}` }
                    });
                    const pd = payRes.data;
                    console.log(`  Net Received: $${pd.net_received_amount}`);
                    console.log(`  Marketplace Fee: $${pd.marketplace_fee}`);
                    console.log(`  Coupon Amount: $${pd.coupon_amount}`);
                    totalNet += pd.net_received_amount || 0;
                } catch (e) { console.log(`  Error: ${e.message}`); }
            }
        }
    }

    // Taxes
    let taxAmount = 0;
    const revenue = oi.unit_price * oi.quantity;
    const fee = oi.sale_fee * oi.quantity;
    const theoNet = revenue - fee - shipCost;
    if (totalNet > 0) {
        const gap = theoNet - totalNet;
        console.log(`\nTheoretical Net: $${theoNet.toFixed(2)}`);
        console.log(`Actual Net Received: $${totalNet.toFixed(2)}`);
        console.log(`Gap (Hidden Tax): $${gap.toFixed(2)}`);
        if (gap > 1.0) taxAmount = gap;
    }

    // 4. Cost from Google Sheets
    console.log(`\n--- COSTO SHEET ---`);
    const creds = require('./credentials.json');
    const auth = new JWT({ email: creds.client_email, key: creds.private_key, scopes: ['https://www.googleapis.com/auth/spreadsheets'] });
    const doc = new GoogleSpreadsheet(process.env.SHEET_ID, auth);
    await doc.loadInfo();

    // Check in 20x20mm tab (user said Row 82)
    const sheet = doc.sheetsByTitle['20x20mm'];
    if (sheet) {
        await sheet.loadCells('A1:U100');
        // Row 82 (0-indexed = 81)
        const r = 81;
        const skuCell = sheet.getCell(r, 0);
        const costCell = sheet.getCell(r, 2); // Column C
        const marginCell = sheet.getCell(r, 19); // Column T

        console.log(`Row 82 - SKU: ${skuCell.value}`);
        console.log(`Row 82 - Cost (Col C): ${costCell.value} (formatted: ${costCell.formattedValue})`);
        console.log(`Row 82 - Margin (Col T): ${marginCell.value} (formatted: ${marginCell.formattedValue})`);

        // Also check what title says
        const titleCell = sheet.getCell(r, 1); // Column B
        console.log(`Row 82 - Title (Col B): ${titleCell.value}`);

        // Check price in sheet
        // Try columns around common positions
        for (let c = 0; c < 21; c++) {
            const cell = sheet.getCell(r, c);
            const hdr = sheet.getCell(0, c);
            if (cell.value !== null && cell.value !== '') {
                console.log(`  Col ${c} (${hdr.value}): ${cell.formattedValue || cell.value}`);
            }
        }
    }

    // Also search SKU 8700 across all tabs
    console.log(`\n--- BÚSQUEDA SKU 8700 en TODAS las pestañas ---`);
    const tabs = ['10x10mm', '15x15mm', '20x20mm', '25x25mm', '40x40mm', '50x50mm', '50x150mm', 'Mosquitero', 'Cerco', 'Rombos'];
    for (const t of tabs) {
        const s = doc.sheetsByTitle[t];
        if (!s) continue;
        try {
            await s.loadCells('A1:C300');
            for (let r = 0; r < 300; r++) {
                const sv = String(s.getCell(r, 0).value || '').trim();
                if (sv === '8700') {
                    const cv = s.getCell(r, 2);
                    console.log(`  Found in '${t}' row ${r + 1}: Cost = ${cv.formattedValue || cv.value}`);
                }
            }
        } catch (e) { }
    }

    // 5. Final Calculation
    console.log(`\n=== MI CÁLCULO ===`);
    const unitCost = 10178; // What my script found
    const totalCost = unitCost * oi.quantity;
    const netBeforeAdv = revenue - fee - shipCost - taxAmount - logCost;
    const advCost = netBeforeAdv * 0.046;
    const netInHand = netBeforeAdv - advCost;
    const profit = netInHand - totalCost;
    const margin = (profit / revenue) * 100;

    console.log(`(+) Revenue:       $${revenue}`);
    console.log(`(-) ML Fee:        -$${fee}`);
    console.log(`(-) Shipping:      -$${shipCost}`);
    console.log(`(-) Flex Logist:   -$${logCost}`);
    console.log(`(-) Tax:           -$${taxAmount.toFixed(2)}`);
    console.log(`(=) Net Pre-Adel:  $${netBeforeAdv.toFixed(2)}`);
    console.log(`(-) Adelanto 4.6%: -$${advCost.toFixed(2)}`);
    console.log(`(=) Net en Mano:   $${netInHand.toFixed(2)}`);
    console.log(`(-) Costo Prod:    -$${totalCost}`);
    console.log(`=========================`);
    console.log(`(=) GANANCIA:      $${profit.toFixed(2)}`);
    console.log(`(%) MARGEN:        ${margin.toFixed(2)}%`);
}

debugSku8700();
