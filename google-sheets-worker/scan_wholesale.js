const axios = require('axios');
const fs = require('fs');

async function scanWholesaleByTag() {
    try {
        const token = fs.readFileSync('ml_token.txt', 'utf8').trim();
        const userId = '97128565';

        let allIds = [];
        let scrollId = null;
        console.log("Fetching IDs (Scroll)...");
        try {
            while (true) {
                let url = `https://api.mercadolibre.com/users/${userId}/items/search?search_type=scan&status=active&limit=100`;
                if (scrollId) url += `&scroll_id=${scrollId}`;
                const res = await axios.get(url, { headers: { Authorization: `Bearer ${token}` } });
                const results = res.data.results || [];
                if (results.length === 0) break;
                allIds = allIds.concat(results);
                scrollId = res.data.scroll_id;
            }
        } catch (e) { }

        console.log(`Scanning ${allIds.length} items for tag 'standard_price_by_quantity'...`);

        const BATCH_SIZE = 20; // Reduced
        let found = [];

        for (let i = 0; i < allIds.length; i += BATCH_SIZE) {
            const batch = allIds.slice(i, i + BATCH_SIZE);
            if (batch.length === 0) continue;

            try {
                const batchRes = await axios.get(`https://api.mercadolibre.com/items?ids=${batch.join(',')}`, {
                    headers: { Authorization: `Bearer ${token}` }
                });

                for (const item of batchRes.data) {
                    if (item.code === 200) {
                        const data = item.body;
                        if (data.tags && data.tags.includes('standard_price_by_quantity')) {
                            found.push(`${data.title} (ID: ${data.id}) - $${data.price}`);
                        }
                    }
                }
            } catch (e) { console.error(e.message); }
            process.stdout.write(`\rScanned ${Math.min(i + BATCH_SIZE, allIds.length)}`);
        }

        console.log(`\n\n--- FOUND ITEMS (${found.length}) ---`);
        found.forEach(f => console.log(f));

    } catch (e) { console.error(e.message); }
}

scanWholesaleByTag();
