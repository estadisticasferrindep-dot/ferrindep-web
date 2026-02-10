const axios = require('axios');
const fs = require('fs');

async function inspectItem() {
    try {
        const token = fs.readFileSync('ml_token.txt', 'utf8').trim();
        const itemId = 'MLA1399082396'; // Sample from previous list

        console.log(`Fetching details for ${itemId}...`);
        const response = await axios.get(`https://api.mercadolibre.com/items/${itemId}`, {
            headers: { 'Authorization': `Bearer ${token}` }
        });

        const item = response.data;

        console.log('--- Seller Custom Field ---');
        console.log(item.seller_custom_field);

        console.log('\n--- Attributes (Searching for SKU) ---');
        item.attributes.forEach(attr => {
            if (attr.id === 'SELLER_SKU' || attr.name.includes('SKU') || attr.id === 'GTIN') {
                console.log(`[${attr.id}] ${attr.name}: ${attr.value_name}`);
            }
        });

        // console.log('\n--- Full JSON (Truncated) ---');
        // console.log(JSON.stringify(item, null, 2).slice(0, 2000));

    } catch (error) {
        console.error(error);
    }
}

inspectItem();
