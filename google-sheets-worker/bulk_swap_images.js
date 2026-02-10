const fs = require('fs');
const axios = require('axios');

const ITEMS = [
    'MLA2066766616',
    'MLA1492685845',
    'MLA910246716',
    'MLA1492786331',
    'MLA820655701',
    'MLA1509811477',
    'MLA910246829',
    'MLA819858294',
    'MLA1560947277',
    'MLA1560654855',
    'MLA2068265936'
];

async function bulkSwap() {
    try {
        const token = fs.readFileSync('ml_token.txt', 'utf8').trim();
        if (!token) throw new Error('No token found in ml_token.txt');

        console.log(`Starting Batch Process for ${ITEMS.length} items...`);
        console.log('Swapping Position 3 (Index 2) <-> Position 4 (Index 3)\n');

        for (const itemId of ITEMS) {
            try {
                console.log(`Processing ${itemId}...`);

                // 1. Get Item
                const itemRes = await axios.get(`https://api.mercadolibre.com/items/${itemId}`, {
                    headers: { Authorization: `Bearer ${token}` }
                });

                let pictures = itemRes.data.pictures;

                if (pictures.length < 4) {
                    console.warn(`  [SKIP] Item has only ${pictures.length} images. Need at least 4.`);
                    continue;
                }

                // 2. Prepare IDs for update
                let pictureIds = pictures.map(p => ({ id: p.id }));

                // 3. Swap Index 2 and 3
                const temp = pictureIds[2];
                pictureIds[2] = pictureIds[3];
                pictureIds[3] = temp;

                // 4. Update
                await axios.put(`https://api.mercadolibre.com/items/${itemId}`, {
                    pictures: pictureIds
                }, {
                    headers: { Authorization: `Bearer ${token}` }
                });

                console.log(`  [OK] Images swapped.`);

            } catch (itemErr) {
                console.error(`  [ERROR] Failed to update ${itemId}:`, itemErr.response ? itemErr.response.data.message : itemErr.message);
            }
            // Small delay to be polite to API
            await new Promise(r => setTimeout(r, 500));
        }

        console.log('\nBatch Process Completed.');

    } catch (e) {
        console.error('Fatal Error:', e.message);
    }
}

bulkSwap();
