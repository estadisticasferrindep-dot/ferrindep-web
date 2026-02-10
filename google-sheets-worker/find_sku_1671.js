const axios = require('axios');
const fs = require('fs');

async function findBySku() {
    const token = fs.readFileSync('ml_token.txt', 'utf8').trim();
    let offset = 0;
    const LIMIT = 50;

    console.log(`Searching orders for SKU 1671...`);
    // Cannot filter by SKU in orders search directly.
    // Must scan recent orders and filter in code.
    // I'll scan 1000 again but log ALL orders with SKU 1671.

    while (offset < 2000) {
        try {
            const res = await axios.get(`https://api.mercadolibre.com/orders/search?seller=97128565&order.status=paid&sort=date_desc&limit=${LIMIT}&offset=${offset}`, {
                headers: { Authorization: `Bearer ${token}` }
            });
            const batch = res.data.results || [];
            if (batch.length === 0) break;

            for (const o of batch) {
                const sku = o.order_items[0].item.seller_sku;
                if (sku === '1671') {
                    console.log(`[MATCH] Order: ${o.id} | Date: ${o.date_created} | Buyer: ${o.buyer.nickname}`);
                    if (String(o.id).endsWith('1493')) {
                        console.log(`!!! FOUND EXACT MATCH ENDING IN 1493 !!!`);
                        fs.writeFileSync('target_order_id.txt', String(o.id));
                        return;
                    }
                }
            }
            offset += LIMIT;
            process.stdout.write('.');
        } catch (e) {
            console.error(e.message);
            break;
        }
    }
}

findBySku();
