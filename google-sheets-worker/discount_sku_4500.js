const axios = require('axios');
const fs = require('fs');

async function applyDiscount() {
    const token = fs.readFileSync('ml_token.txt', 'utf8').trim();
    const userId = '97128565';
    const targetSku = '4500';

    // 1. Find Item ID by SKU via ML API
    console.log(`Searching for SKU ${targetSku} via API...`);
    let allIds = [];
    let scrollId = null;

    while (true) {
        let url = `https://api.mercadolibre.com/users/${userId}/items/search?search_type=scan&status=active&limit=100`;
        if (scrollId) url += `&scroll_id=${scrollId}`;
        const res = await axios.get(url, { headers: { Authorization: `Bearer ${token}` } });
        const results = res.data.results || [];
        if (results.length === 0) break;
        allIds = allIds.concat(results);
        scrollId = res.data.scroll_id;
    }

    console.log(`Checking ${allIds.length} items for SKU ${targetSku}...`);

    let itemId = null;
    let currentPrice = null;
    let title = null;

    for (let i = 0; i < allIds.length; i += 20) {
        const chunk = allIds.slice(i, i + 20);
        const batchRes = await axios.get(`https://api.mercadolibre.com/items?ids=${chunk.join(',')}`, {
            headers: { Authorization: `Bearer ${token}` }
        });

        for (const item of batchRes.data) {
            if (item.code === 200) {
                const skuAttr = item.body.attributes.find(a => a.id === 'SELLER_SKU');
                if (skuAttr && skuAttr.value_name === targetSku) {
                    itemId = item.body.id;
                    currentPrice = item.body.price;
                    title = item.body.title;
                    break;
                }
            }
        }
        if (itemId) break;
    }

    if (!itemId) {
        console.error(`SKU ${targetSku} not found!`);
        return;
    }

    console.log(`Found: ${itemId} - ${title} - $${currentPrice}`);

    // 2. Apply 5% Discount for 3 Days
    const dealPrice = Math.floor(currentPrice * 0.95);
    const now = new Date();
    const end = new Date();
    end.setDate(now.getDate() + 3);

    const payload = {
        promotion_type: 'PRICE_DISCOUNT',
        deal_price: dealPrice,
        start_date: now.toISOString().split('.')[0],
        finish_date: end.toISOString().split('.')[0],
        original_price: currentPrice,
        name: `Descuento 5% SKU ${targetSku}`
    };

    console.log(`Applying: $${currentPrice} -> $${dealPrice} (5% off, 3 days)...`);

    try {
        await axios.post(
            `https://api.mercadolibre.com/seller-promotions/items/${itemId}?app_version=v2`,
            payload,
            { headers: { Authorization: `Bearer ${token}` } }
        );
        console.log(`[SUCCESS] Discount applied!`);
    } catch (e) {
        console.error(`[FAIL] ${e.message}`);
        if (e.response) console.error(JSON.stringify(e.response.data, null, 2));
    }
}

applyDiscount();
