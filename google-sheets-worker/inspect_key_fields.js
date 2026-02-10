const axios = require('axios');
const fs = require('fs');

async function inspectItemKeyFields() {
    try {
        const token = fs.readFileSync('ml_token.txt', 'utf8').trim();
        const itemId = 'MLA640050474';

        const res = await axios.get(`https://api.mercadolibre.com/items/${itemId}`, {
            headers: { Authorization: `Bearer ${token}` }
        });

        const data = res.data;
        console.log("--- Item Key Fields ---");
        console.log(`ID: ${data.id}`);
        console.log(`Title: ${data.title}`);
        console.log(`Price: ${data.price}`);
        console.log(`Base Price: ${data.base_price}`);
        console.log(`Original Price: ${data.original_price}`);
        console.log(`Pricing Scheme:`, JSON.stringify(data.pricing_scheme, null, 2));
        console.log(`Sale Terms:`, JSON.stringify(data.sale_terms, null, 2));
        console.log(`Attributes:`, JSON.stringify(data.attributes, null, 2));
        console.log(`Variations:`, JSON.stringify(data.variations, null, 2));
        console.log(`Listing Type: ${data.listing_type_id}`);
        console.log(`Catalog Product ID: ${data.catalog_product_id}`);

        // Also check /items/{id}/prices/types if accessible?
        // Or /items/{id}/health/actions ??
    } catch (e) {
        console.error(e.message);
    }
}

inspectItemKeyFields();
