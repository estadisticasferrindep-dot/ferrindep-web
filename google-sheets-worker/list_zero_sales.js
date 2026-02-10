const axios = require('axios');
const fs = require('fs');

async function listZeroSales() {
    try {
        const token = fs.readFileSync('ml_token.txt', 'utf8').trim();

        // 1. Get Me
        const meRes = await axios.get('https://api.mercadolibre.com/users/me', {
            headers: { 'Authorization': `Bearer ${token}` }
        });
        const userId = meRes.data.id;
        console.log(`User ID: ${userId} - Fetching items...`);

        // 2. Search all active item IDs
        let allIds = [];
        let offset = 0;
        const limit = 50;
        let total = 0;

        do {
            const searchRes = await axios.get(`https://api.mercadolibre.com/users/${userId}/items/search?status=active&limit=${limit}&offset=${offset}`, {
                headers: { 'Authorization': `Bearer ${token}` }
            });
            const results = searchRes.data.results || [];
            allIds = allIds.concat(results);
            total = searchRes.data.paging.total;
            offset += limit;
            process.stdout.write(`\rFound ${allIds.length} / ${total} active items...`);
        } while (offset < total && offset < 1000); // Safety cap at 1000 for now, remove for full run if needed

        console.log(`\nAnalyzing ${allIds.length} items details...`);

        // 3. Batch fetch details (chunk by 20 for multiget)
        let zeroSalesCount = 0;
        const chunkSize = 20;

        console.log("\n--- PUBLICACIONES SIN VENTAS (NO CATALOGO) ---");

        for (let i = 0; i < allIds.length; i += chunkSize) {
            const chunk = allIds.slice(i, i + chunkSize);
            const idsStr = chunk.join(',');

            const detailsRes = await axios.get(`https://api.mercadolibre.com/items?ids=${idsStr}`, {
                headers: { 'Authorization': `Bearer ${token}` }
            });

            detailsRes.data.forEach(itemData => {
                const item = itemData.body;
                if (!item || item.error) return;

                // FILTER: Zero Sales + NOT Catalog Listing
                if (item.sold_quantity === 0 && item.catalog_listing !== true) {

                    // Find SKU in attributes
                    const skuAttr = item.attributes.find(a => a.id === 'SELLER_SKU');
                    const sku = skuAttr ? skuAttr.value_name : 'No SKU'; // Or check GTIN if needed

                    if (sku !== 'No SKU') {
                        console.log(sku);
                    } else {
                        // console.log(`No SKU for ${item.id}`); // Optional debug
                    }
                    zeroSalesCount++;
                }
            });

            // Brief pause to be nice to API limits
            await new Promise(r => setTimeout(r, 200));
        }

        console.log(`\nTotal found: ${zeroSalesCount}`);

    } catch (error) {
        console.error('\nError:', error.response ? error.response.data : error.message);
    }
}

listZeroSales();
