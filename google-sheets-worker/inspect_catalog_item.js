const axios = require('axios');
const fs = require('fs');

async function inspectCatalogItem() {
    try {
        const token = fs.readFileSync('ml_token.txt', 'utf8').trim();
        const itemId = 'MLA2472352522';

        const res = await axios.get(`https://api.mercadolibre.com/items/${itemId}`, {
            headers: { Authorization: `Bearer ${token}` }
        });

        const data = res.data;
        console.log(`--- Inspecting ${itemId} ---`);
        console.log(`Title: ${data.title}`);
        console.log(`Catalog Listing: ${data.catalog_listing}`);
        console.log(`Pricing Scheme:`, JSON.stringify(data.pricing_scheme, null, 2));
        console.log(`Tags:`, JSON.stringify(data.tags, null, 2));

    } catch (e) {
        console.error(e.message);
    }
}

inspectCatalogItem();
