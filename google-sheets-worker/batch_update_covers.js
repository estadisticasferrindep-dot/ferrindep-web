const axios = require('axios');
const fs = require('fs');
const FormData = require('form-data');

const ITEMS = [
    'MLA1392443151',
    'MLA1392467691',
    'MLA1392482141',
    'MLA1392482247',
    'MLA1452318548'
];

const IMAGE_PATH = 'C:\\Users\\mauro\\.gemini\\antigravity\\brain\\7815fdf3-04a5-4d90-a3c9-fbd9ef86effb\\media__1770595202995.jpg';

async function batchUpdateCovers() {
    const token = fs.readFileSync('ml_token.txt', 'utf8').trim();

    for (const itemId of ITEMS) {
        try {
            console.log(`\n--- Processing ${itemId} ---`);

            // 1. Upload image (each item needs its own upload)
            const form = new FormData();
            form.append('file', fs.createReadStream(IMAGE_PATH));

            const uploadRes = await axios.post(
                'https://api.mercadolibre.com/pictures/items/upload',
                form,
                { headers: { Authorization: `Bearer ${token}`, ...form.getHeaders() } }
            );
            const newPicId = uploadRes.data.id;
            console.log(`  Uploaded: ${newPicId}`);

            // 2. Get current pictures
            const itemRes = await axios.get(`https://api.mercadolibre.com/items/${itemId}`, {
                headers: { Authorization: `Bearer ${token}` }
            });
            const currentPics = itemRes.data.pictures || [];
            console.log(`  Title: ${itemRes.data.title}`);
            console.log(`  Current pics: ${currentPics.length}`);

            // 3. New cover first, then rest (skip old cover)
            const newPictures = [{ id: newPicId }];
            for (let i = 1; i < currentPics.length; i++) {
                newPictures.push({ id: currentPics[i].id });
            }

            // 4. Update item
            const updateRes = await axios.put(
                `https://api.mercadolibre.com/items/${itemId}`,
                { pictures: newPictures },
                { headers: { Authorization: `Bearer ${token}` } }
            );
            console.log(`  [SUCCESS] Cover updated!`);

        } catch (e) {
            console.error(`  [FAIL] ${itemId}: ${e.message}`);
            if (e.response) console.error(JSON.stringify(e.response.data, null, 2));
        }
    }

    console.log('\n--- ALL DONE ---');
}

batchUpdateCovers();
