const fs = require('fs');
const axios = require('axios');

const ITEM_ID = 'MLA1427437453';

async function swapImages() {
    try {
        const token = fs.readFileSync('ml_token.txt', 'utf8').trim();
        if (!token) throw new Error('No token found in ml_token.txt');

        console.log(`Fetching item ${ITEM_ID}...`);
        const itemRes = await axios.get(`https://api.mercadolibre.com/items/${ITEM_ID}`, {
            headers: { Authorization: `Bearer ${token}` }
        });

        let pictures = itemRes.data.pictures;
        console.log(`Current picture count: ${pictures.length}`);

        if (pictures.length < 5) {
            console.error('Error: Item has fewer than 5 images. Cannot swap 4th and 5th.');
            return;
        }

        console.log('Current IDs (first 5):');
        pictures.slice(0, 5).forEach((p, i) => console.log(`${i + 1}: ${p.id}`));

        // Prepare the array for update (only IDs are needed for reordering)
        let pictureIds = pictures.map(p => ({ id: p.id }));

        // Swap index 3 (4th) and index 4 (5th)
        const temp = pictureIds[3];
        pictureIds[3] = pictureIds[4];
        pictureIds[4] = temp;

        console.log('Swapping 4 and 5...');
        console.log('New order IDs (first 5):');
        pictureIds.slice(0, 5).forEach((p, i) => console.log(`${i + 1}: ${p.id}`));

        // Update item
        const updateRes = await axios.put(`https://api.mercadolibre.com/items/${ITEM_ID}`, {
            pictures: pictureIds
        }, {
            headers: { Authorization: `Bearer ${token}` }
        });

        console.log('SUCCESS! Images updated.');
        // Verify new order returned
        updateRes.data.pictures.slice(0, 5).forEach((p, i) => console.log(`${i + 1}: ${p.id}`));

    } catch (e) {
        console.error('Error:', e.message);
        if (e.response) console.error('API Response:', JSON.stringify(e.response.data, null, 2));
    }
}

swapImages();
