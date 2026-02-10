const fs = require('fs');
const axios = require('axios');

const SOURCE_IMAGE_URL = 'http://http2.mlstatic.com/D_853254-MLA98649747497_112025-O.jpg';

const TARGET_ITEMS = [
    'MLA801222637',
    'MLA2534510664',
    'MLA801225506',
    'MLA801057163'
];

async function updateCovers() {
    try {
        const token = fs.readFileSync('ml_token.txt', 'utf8').trim();
        console.log(`Starting Batch Update for ${TARGET_ITEMS.length} items...`);
        console.log(`Target Cover: ${SOURCE_IMAGE_URL}\n`);

        for (const itemId of TARGET_ITEMS) {
            try {
                process.stdout.write(`Processing ${itemId}... `);

                // 1. Get Item to see current photos
                const itemRes = await axios.get(`https://api.mercadolibre.com/items/${itemId}`, {
                    headers: { Authorization: `Bearer ${token}` }
                });

                const currentPictures = itemRes.data.pictures;

                // Safety check for max images (assuming 12 is a safe upper limit, though generic is often 10-12)
                if (currentPictures.length >= 12) {
                    console.log(`[SKIP] Full capacity (${currentPictures.length} images). Cannot add more.`);
                    continue;
                }

                // 2. Construct Payload
                // New Order: [ { source: URL }, ...ExistingIDs ]
                const existingIds = currentPictures.map(p => ({ id: p.id }));
                const newPictures = [{ source: SOURCE_IMAGE_URL }, ...existingIds];

                // 3. Update Item
                await axios.put(`https://api.mercadolibre.com/items/${itemId}`, {
                    pictures: newPictures
                }, {
                    headers: { Authorization: `Bearer ${token}` }
                });

                console.log(`[OK] Added cover. Total photos: ${newPictures.length}`);

            } catch (err) {
                const msg = err.response ? JSON.stringify(err.response.data) : err.message;
                console.log(`[ERROR] ${msg}`);
            }
            // Polite delay
            await new Promise(r => setTimeout(r, 1000));
        }

        console.log('\nBatch Completed.');

    } catch (e) {
        console.error('Fatal Error:', e.message);
    }
}

updateCovers();
