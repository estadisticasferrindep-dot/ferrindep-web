const axios = require('axios');
const fs = require('fs');

const OUTPUT_FILE = 'ml_products_db.json';
const TOKEN_FILE = 'ml_token.txt';

async function fetchMLProducts() {
    try {
        console.log("🚀 Starting MercadoLibre Link Fetcher...");

        // 1. Read Token
        if (!fs.existsSync(TOKEN_FILE)) {
            throw new Error(`Token file '${TOKEN_FILE}' not found.`);
        }
        const token = fs.readFileSync(TOKEN_FILE, 'utf8').trim();

        // 2. Get User ID
        console.log("👤 Fetching User Info...");
        const meRes = await axios.get('https://api.mercadolibre.com/users/me', {
            headers: { 'Authorization': `Bearer ${token}` }
        });
        const userId = meRes.data.id;
        console.log(`✅ User ID: ${userId} (${meRes.data.nickname})`);

        // 3. Search All Active Items
        let offset = 0;
        const limit = 50;
        let allItemIds = [];
        let total = 0;

        console.log("🔎 Searching active items...");
        do {
            const searchUrl = `https://api.mercadolibre.com/users/${userId}/items/search?status=active&limit=${limit}&offset=${offset}`;
            try {
                const searchRes = await axios.get(searchUrl, {
                    headers: { 'Authorization': `Bearer ${token}` }
                });

                const results = searchRes.data.results || [];
                if (results.length > 0) {
                    allItemIds = allItemIds.concat(results);
                }

                total = searchRes.data.paging.total;
                offset += limit;

                process.stdout.write(`...Found ${allItemIds.length} / ${total}\r`);

                // Safety break for standard API limit (usually 1000 without scan)
                if (offset >= 1000) {
                    console.log("\n⚠️ Offset limit reached (1000). Stopping search to prevent API errors.", total);
                    break;
                }

            } catch (err) {
                if (err.response && err.response.status === 400) {
                    console.log("\n⚠️ Reached API Pagination Limit (400). Stopping matching.");
                    break; // Exit loop gracefully
                }
                throw err; // Rethrow other errors
            }

        } while (offset < total);

        console.log(`\n📦 Total Active Items Found: ${allItemIds.length}`);

        // 4. Fetch Details (Titles & Permalinks) in batches of 20 (Multiget limit)
        console.log("📥 Fetching details (Titles & Links)...");

        let productsDB = [];
        const batchSize = 20;

        for (let i = 0; i < allItemIds.length; i += batchSize) {
            const batch = allItemIds.slice(i, i + batchSize);
            const idsString = batch.join(',');

            const detailsRes = await axios.get(`https://api.mercadolibre.com/items?ids=${idsString}&attributes=id,title,permalink,price`, {
                headers: { 'Authorization': `Bearer ${token}` }
            });

            // detailsRes.data is an array of objects { code, body }
            detailsRes.data.forEach(itemWrapper => {
                if (itemWrapper.code === 200) {
                    const item = itemWrapper.body;
                    productsDB.push({
                        id: item.id,
                        title: item.title,
                        price: item.price, // Save Price
                        permalink: item.permalink,
                        // Simple Keyword Generation from Title
                        keywords: item.title.toLowerCase().split(' ').filter(w => w.length > 2)
                    });
                }
            });

            process.stdout.write(`...Processed ${productsDB.length} / ${allItemIds.length}\r`);
        }

        console.log(`\n💾 Saving to ${OUTPUT_FILE}...`);
        fs.writeFileSync(OUTPUT_FILE, JSON.stringify(productsDB, null, 2));
        console.log("✅ Done! Database updated.");

    } catch (error) {
        console.error("❌ Error:", error.response ? error.response.data : error.message);
    }
}

fetchMLProducts();
