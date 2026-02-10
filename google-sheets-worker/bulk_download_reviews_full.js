const axios = require('axios');
const fs = require('fs');
const path = require('path');

// No limit - Scan ALL active items
async function bulkDownloadFull() {
    const token = fs.readFileSync('ml_token.txt', 'utf8').trim();
    const userId = '97128565';

    console.log(`Starting FULL SCAN of active items...`);

    let scrollId = null;
    let totalScanned = 0;
    let totalPhotosFound = 0;

    // Use a loop that processes in chunks to avoid holding 1200 items in memory?
    // Actually 1200 strings is tiny. We can fetch all IDs first.

    let allIds = [];
    console.log("Fetching Item IDs...");

    try {
        while (true) {
            let url = `https://api.mercadolibre.com/users/${userId}/items/search?search_type=scan&status=active&limit=100`;
            if (scrollId) url += `&scroll_id=${scrollId}`;

            const searchRes = await axios.get(url, { headers: { Authorization: `Bearer ${token}` } });
            const results = searchRes.data.results || [];

            if (results.length === 0) break;

            allIds = allIds.concat(results);
            scrollId = searchRes.data.scroll_id;
            process.stdout.write(`\rIds found: ${allIds.length}`);
        }
    } catch (e) { console.error("\nSearch Error: " + e.message); return; }

    console.log(`\n\nIdentified ${allIds.length} active items. Starting download process...`);

    // Process in batches of 20 to fetch details
    const BATCH_SIZE = 20;

    for (let i = 0; i < allIds.length; i += BATCH_SIZE) {
        const chunk = allIds.slice(i, i + BATCH_SIZE);

        try {
            // 1. Get Details for Folder Naming
            const detRes = await axios.get(`https://api.mercadolibre.com/items?ids=${chunk.join(',')}`, {
                headers: { Authorization: `Bearer ${token}` }
            });

            let itemMap = {};
            detRes.data.forEach(d => { if (d.code === 200) itemMap[d.body.id] = d.body; });

            // 2. Fetch Reviews for each item in chunk
            // We must do this sequentially or parallelized carefully. 
            // 20 parallel requests might hit rate limits. Let's do 5 at a time or sequential.
            // Sequential is safer for long running.

            for (const itemId of chunk) {
                const item = itemMap[itemId];
                if (!item) continue;

                try {
                    const revRes = await axios.get(`https://api.mercadolibre.com/reviews/item/${itemId}?limit=50`, {
                        headers: { Authorization: `Bearer ${token}` }
                    });
                    const reviews = revRes.data.reviews || [];

                    let photosToDownload = [];
                    for (const r of reviews) {
                        if (r.media && r.media.length > 0) {
                            for (const m of r.media) {
                                let url = null;
                                if (m.variations && m.variations.length > 0) url = m.variations[0].url;
                                else if (m.link) url = m.link;
                                if (url) photosToDownload.push({ url, reviewId: r.id });
                            }
                        }
                    }

                    if (photosToDownload.length > 0) {
                        // Naming
                        let sku = 'NOSKU';
                        const skuAttr = item.attributes.find(a => a.id === 'SELLER_SKU');
                        if (skuAttr) sku = skuAttr.value_name;

                        let model = 'NOMODEL';
                        const modelAttr = item.attributes.find(a => a.id === 'MODEL');
                        if (modelAttr) model = modelAttr.value_name;

                        const folderName = `${sku}_${model}_${itemId}`.replace(/[\/\\:*?"<>|]/g, '-');
                        const outDir = path.join(__dirname, 'downloaded_photos', folderName);

                        if (!fs.existsSync(outDir)) fs.mkdirSync(outDir, { recursive: true });

                        process.stdout.write(`\n[${i + 1}/${allIds.length}] ${folderName}: ${photosToDownload.length} photos`);

                        let pCount = 0;
                        for (const p of photosToDownload) {
                            pCount++;
                            const filename = `review_${p.reviewId}_${pCount}.jpg`;
                            const dest = path.join(outDir, filename);
                            if (!fs.existsSync(dest)) {
                                await downloadImage(p.url, dest);
                            }
                        }
                        totalPhotosFound += photosToDownload.length;
                    }

                } catch (e) { /* Ignore review 404s/errors */ }
            }

        } catch (e) { console.error(`Batch Error: ${e.message}`); }

        process.stdout.write(`\rProcessed ${Math.min(i + BATCH_SIZE, allIds.length)}/${allIds.length}...`);
    }

    console.log(`\n\n--- FULL SCAN COMPLETE ---`);
    console.log(`Total items scanned: ${allIds.length}`);
}

async function downloadImage(url, filepath) {
    try {
        const writer = fs.createWriteStream(filepath);
        const response = await axios({ url, method: 'GET', responseType: 'stream' });
        response.data.pipe(writer);
        return new Promise((resolve, reject) => {
            writer.on('finish', resolve);
            writer.on('error', reject);
        });
    } catch (e) { }
}

bulkDownloadFull();
