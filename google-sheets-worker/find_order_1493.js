const axios = require('axios');
const fs = require('fs');

async function findOrder() {
    const token = fs.readFileSync('ml_token.txt', 'utf8').trim();
    let offset = 0;
    const LIMIT = 50;
    const MAX_SEARCH = 1000;

    console.log(`Searching for order ending in '1493' in last ${MAX_SEARCH} orders...`);

    while (offset < MAX_SEARCH) {
        try {
            const res = await axios.get(`https://api.mercadolibre.com/orders/search?seller=97128565&order.status=paid&sort=date_desc&limit=${LIMIT}&offset=${offset}`, {
                headers: { Authorization: `Bearer ${token}` }
            });
            const batch = res.data.results || [];
            if (batch.length === 0) break;

            const target = batch.find(o => String(o.id).endsWith('1493'));

            if (target) {
                console.log(`\nFOUND ORDER: ${target.id}`);
                console.log(`Date: ${target.date_created}`);
                target.order_items.forEach(i => {
                    console.log(`- ${i.item.title} (SKU: ${i.item.seller_sku}) x${i.quantity}`);
                });
                fs.writeFileSync('target_order_id.txt', String(target.id));
                return;
            }

            offset += LIMIT;
            process.stdout.write(`.`);
        } catch (e) {
            console.error(e.message);
            break;
        }
    }
    console.log('\nOrder not found.');
}

findOrder();
