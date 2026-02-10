const fs = require('fs');
const axios = require('axios');

const ITEM_ID = 'MLA1427437453';

async function inspectItemPromos() {
    try {
        const token = fs.readFileSync('ml_token.txt', 'utf8').trim();
        if (!token) throw new Error('No token found in ml_token.txt');

        console.log(`Inspecting ${ITEM_ID}...`);

        // 1. Get Item Details (Price, Title)
        const itemRes = await axios.get(`https://api.mercadolibre.com/items/${ITEM_ID}`, {
            headers: { Authorization: `Bearer ${token}` }
        });
        const item = itemRes.data;
        console.log(`Title: ${item.title}`);
        console.log(`Price: ${item.price} (Original: ${item.original_price || 'None'})`);
        console.log(`Status: ${item.status}`);
        console.log(`Listing Type: ${item.listing_type_id}`);

        // 2. Get Available Promotions (Seller Promotions)
        // Endpoint: /seller-promotions/items/{ITEM_ID}
        // Returns active and available promotions
        console.log('\nFetching Seller Promotions...');
        try {
            const promoRes = await axios.get(`https://api.mercadolibre.com/seller-promotions/items/${ITEM_ID}`, {
                headers: { Authorization: `Bearer ${token}` },
                params: { app_version: 'v2' }
            });

            const promos = promoRes.data;
            // Usually returns array of promotions where item is applicable
            // Check structure
            console.log(JSON.stringify(promos, null, 2));

        } catch (promoErr) {
            console.error('Error fetching promotions:', promoErr.response ? promoErr.response.data : promoErr.message);
        }

    } catch (e) {
        console.error('Error:', e.response ? e.response.data : e.message);
    }
}

inspectItemPromos();
