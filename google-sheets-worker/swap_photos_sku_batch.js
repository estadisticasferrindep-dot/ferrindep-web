const axios = require('axios');
const fs = require('fs');

async function swapPhotos() {
    const targetSKUs = ['3880', '140', '5675', '5680'];

    try {
        const token = fs.readFileSync('ml_token.txt', 'utf8').trim();

        // 1. Get Me
        const meRes = await axios.get('https://api.mercadolibre.com/users/me', {
            headers: { 'Authorization': `Bearer ${token}` }
        });
        const userId = meRes.data.id;
        console.log(`User ID: ${userId}`);

        for (const sku of targetSKUs) {
            console.log(`\n--- Processing SKU: ${sku} ---`);

            // 2. Search Item by SKU
            // Note: 'seller_sku' filter on search sometimes works, or just 'q'
            const searchRes = await axios.get(`https://api.mercadolibre.com/users/${userId}/items/search?seller_sku=${sku}`, {
                headers: { 'Authorization': `Bearer ${token}` }
            });

            if (searchRes.data.results.length === 0) {
                console.log(`❌ Details: Item not found for SKU ${sku}`);
                continue;
            }

            const itemId = searchRes.data.results[0];
            console.log(`✅ Found Item ID: ${itemId}`);

            // 3. Fetch Item Details (Pictures)
            const itemRes = await axios.get(`https://api.mercadolibre.com/items/${itemId}`, {
                headers: { 'Authorization': `Bearer ${token}` }
            });
            const item = itemRes.data;
            const pics = item.pictures;

            console.log(`   Total Photos: ${pics.length}`);

            if (pics.length < 4) {
                console.log(`⚠️ Warning: Less than 4 photos. Cannot swap 3rd and 4th.`);
                continue;
            }

            // 4. Swap Photos (Index 2 and 3)
            // Log current IDs
            // console.log(`   Pre-Swap: [0]${pics[0].id} [1]${pics[1].id} [2]${pics[2].id} [3]${pics[3].id}`);

            const newPics = [...pics];
            const temp = newPics[2];
            newPics[2] = newPics[3];
            newPics[3] = temp;

            // console.log(`   Post-Swap: [0]${newPics[0].id} [1]${newPics[1].id} [2]${newPics[2].id} [3]${newPics[3].id}`);

            // 5. Update Item
            // Need to map to array of objects with 'id' only for update
            const picturesPayload = newPics.map(p => ({ id: p.id }));

            console.log(`   Updating item ${itemId}...`);
            const updateRes = await axios.put(`https://api.mercadolibre.com/items/${itemId}`,
                { pictures: picturesPayload },
                { headers: { 'Authorization': `Bearer ${token}` } }
            );

            console.log(`✅ Update Successful! Status: ${updateRes.status}`);
        }

    } catch (error) {
        console.error('Error:', error.response ? error.response.data : error.message);
    }
}

swapPhotos();
