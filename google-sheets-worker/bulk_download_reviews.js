const axios = require('axios');
const fs = require('fs');
const path = require('path');

const BATCH_LIMIT = 200; // Updated to 200

async function bulkDownload() {
    const token = fs.readFileSync('ml_token.txt', 'utf8').trim();
    const userId = '97128565';

    // 1. Fetch Item IDs (Active) - Loop to get BATCH_LIMIT
    console.log(`Fetching ${BATCH_LIMIT} active items...`);
    let itemsToScan = [];
    let scrollId = null;

    try {
        while (itemsToScan.length < BATCH_LIMIT) {
            let url = `https://api.mercadolibre.com/users/${userId}/items/search?search_type=scan&status=active&limit=100`;
            if (scrollId) url += `&scroll_id=${scrollId}`;

            const searchRes = await axios.get(url, {
                headers: { Authorization: `Bearer ${token}` }
            });

            const results = searchRes.data.results || [];
            if (results.length === 0) break;

            const needed = BATCH_LIMIT - itemsToScan.length;
            itemsToScan = itemsToScan.concat(results.slice(0, needed));
            scrollId = searchRes.data.scroll_id;

            if (itemsToScan.length >= BATCH_LIMIT) break;
        }
    } catch (e) { console.error("Search Error: " + e.message); return; }

    console.log(`Scanning ${itemsToScan.length} items...`);

    // 2. Fetch Details for Folder Naming (SKU, Title, Attributes)
    let itemDetails = {};
    for (let i = 0; i < itemsToScan.length; i += 20) {
        const chunk = itemsToScan.slice(i, i + 20);
        try {
            const detRes = await axios.get(`https://api.mercadolibre.com/items?ids=${chunk.join(',')}`, {
                headers: { Authorization: `Bearer ${token}` }
            });
            detRes.data.forEach(d => {
                if (d.code === 200) {
                    itemDetails[d.body.id] = d.body;
                }
            });
        } catch (e) { console.error("Details Error: " + e.message); }
    }

    // 3. Scan Reviews & Download
    let foundCount = 0;

    for (const itemId of itemsToScan) {
        const item = itemDetails[itemId];
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
                foundCount++;
                // Construct Folder Name
                let sku = 'NOSKU';
                const skuAttr = item.attributes.find(a => a.id === 'SELLER_SKU');
                if (skuAttr) sku = skuAttr.value_name;

                let model = 'NOMODEL';
                const modelAttr = item.attributes.find(a => a.id === 'MODEL');
                if (modelAttr) model = modelAttr.value_name;

                // Sanitize folder name
                const safeTitle = (item.title || 'NoTitle').replace(/[^a-z0-9]/gi, '_').substring(0, 30);
                const folderName = `${sku}_${model}_${itemId}`.replace(/[\/\\:*?"<>|]/g, '-');
                const outDir = path.join(__dirname, 'downloaded_photos', folderName);

                if (!fs.existsSync(outDir)) fs.mkdirSync(outDir, { recursive: true });

                console.log(`[FOUND] ${itemId} (${photosToDownload.length} photos) -> ${folderName}`);

                let pCount = 0;
                for (const p of photosToDownload) {
                    pCount++;
                    const filename = `review_${p.reviewId}_${pCount}.jpg`;
                    const dest = path.join(outDir, filename);
                    if (!fs.existsSync(dest)) { // Skip if exists
                        await downloadImage(p.url, dest);
                    }
                }
            }

        } catch (e) {
            // console.error(`Error scanning ${itemId}: ${e.message}`);
        }
        process.stdout.write('.');
    }

    console.log(`\n\nDone. Found photos in ${foundCount} items.`);
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

bulkDownload();
