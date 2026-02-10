const axios = require('axios');
const fs = require('fs');

async function swapPhotosBulk() {
    // Full list from previous step
    const allSkus = [
        '3880', '140', '5675', '5680', '1250', '5190', '2125', '1320', '2675', '1915', '2385', '515', '2320',
        '1335', '1280', '1310', '1230', '9060', '9070', '9055', '9085', '5691', '5901', '5906', '6132', '5935',
        '6142', '6140', '6626', '5943', '9142', '9138', '9108', '9144', '9136', '9116', '9140', '6112', '2881',
        '4389', '4404', '685', '4410', '4412', '4399', '7064', '3742', '7060', '1740', '7046', '7040', '7024',
        '7014', '7044', '7032', '7040', '591', '7038', '7042', '1469', '2980', '6163', '4226', '4194', '6156',
        '350', '4207', '3661', '4201', '2560', '2590', '2530', '2551', '3506', '2600', '196', '1395', '1345',
        '1467', '3655', '3230', '930', '7100', '3515', '3225', '1673', '1240', '4226', '4241', '2540', '4213',
        '4241', '2601', '2540', '5175', '5140', '5205', '5145', '2265', '2521', '490', '7142', '1510', '5923',
        '1510', '7142', '510', '3570', '3565'
    ];

    // Already processed SKUs to exclude
    const processedSkus = ['3880', '140', '5675', '5680'];

    // Filter out processed SKUs
    const targetSKUs = allSkus.filter(s => !processedSkus.includes(s));

    console.log(`Total SKUs in list: ${allSkus.length}`);
    console.log(`Excluding already processed: ${processedSkus.length}`);
    console.log(`Target SKUs to process: ${targetSKUs.length}`);

    try {
        const token = fs.readFileSync('ml_token.txt', 'utf8').trim();

        // 1. Get Me
        const meRes = await axios.get('https://api.mercadolibre.com/users/me', {
            headers: { 'Authorization': `Bearer ${token}` }
        });
        const userId = meRes.data.id;
        console.log(`User ID: ${userId}`);

        let successCount = 0;
        let failCount = 0;
        let skipCount = 0;

        for (const sku of targetSKUs) {
            process.stdout.write(`\nProcessing SKU [${sku}]... `);

            // 2. Search Item by SKU
            try {
                // Using q search as fallback if seller_sku param is finicky
                const searchRes = await axios.get(`https://api.mercadolibre.com/users/${userId}/items/search?seller_sku=${sku}`, {
                    headers: { 'Authorization': `Bearer ${token}` }
                });

                if (searchRes.data.results.length === 0) {
                    console.log(`❌ Not found in search.`);
                    failCount++;
                    continue;
                }

                const itemId = searchRes.data.results[0];

                // 3. Fetch Details
                const itemRes = await axios.get(`https://api.mercadolibre.com/items/${itemId}`, {
                    headers: { 'Authorization': `Bearer ${token}` }
                });
                const item = itemRes.data;
                const pics = item.pictures;

                if (pics.length < 4) {
                    console.log(`⚠️ Skipped: Only ${pics.length} photos (Item ${itemId}).`);
                    skipCount++;
                    continue;
                }

                // 4. Swap Photos (Index 2 and 3)
                const newPics = [...pics];
                const temp = newPics[2];
                newPics[2] = newPics[3];
                newPics[3] = temp;

                // 5. Update Item
                const picturesPayload = newPics.map(p => ({ id: p.id }));

                const updateRes = await axios.put(`https://api.mercadolibre.com/items/${itemId}`,
                    { pictures: picturesPayload },
                    { headers: { 'Authorization': `Bearer ${token}` } }
                );

                if (updateRes.status === 200) {
                    console.log(`✅ Success (Item ${itemId})`);
                    successCount++;
                } else {
                    console.log(`❌ Update Failed (Status ${updateRes.status})`);
                    failCount++;
                }

                // Rate limit guard
                await new Promise(r => setTimeout(r, 200));

            } catch (err) {
                console.log(`❌ Error: ${err.message}`);
                failCount++;
            }
        }

        console.log(`\n\n--- SUMMARY ---`);
        console.log(`Total Processed: ${targetSKUs.length}`);
        console.log(`✅ Success: ${successCount}`);
        console.log(`⚠️ Skipped (<4 photos): ${skipCount}`);
        console.log(`❌ Failed: ${failCount}`);

    } catch (error) {
        console.error('Fatal Error:', error);
    }
}

swapPhotosBulk();
