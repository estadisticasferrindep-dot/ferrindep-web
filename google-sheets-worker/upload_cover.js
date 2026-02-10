const axios = require('axios');
const fs = require('fs');
const FormData = require('form-data');

async function uploadCover() {
    const token = fs.readFileSync('ml_token.txt', 'utf8').trim();
    const itemId = 'MLA1392416857';
    const imagePath = 'C:\\Users\\mauro\\.gemini\\antigravity\\brain\\7815fdf3-04a5-4d90-a3c9-fbd9ef86effb\\media__1770595202995.jpg';

    try {
        console.log('Uploading image to MercadoLibre...');
        const form = new FormData();
        form.append('file', fs.createReadStream(imagePath));

        const uploadRes = await axios.post(
            'https://api.mercadolibre.com/pictures/items/upload',
            form,
            { headers: { Authorization: `Bearer ${token}`, ...form.getHeaders() } }
        );

        const newPicId = uploadRes.data.id;
        console.log(`Image uploaded. Picture ID: ${newPicId}`);

        const itemRes = await axios.get(`https://api.mercadolibre.com/items/${itemId}`, {
            headers: { Authorization: `Bearer ${token}` }
        });

        const currentPics = itemRes.data.pictures || [];
        console.log(`Current pictures: ${currentPics.length}`);

        // New cover first, then rest (skip old cover)
        const newPictures = [{ id: newPicId }];
        for (let i = 1; i < currentPics.length; i++) {
            newPictures.push({ id: currentPics[i].id });
        }

        const updateRes = await axios.put(
            `https://api.mercadolibre.com/items/${itemId}`,
            { pictures: newPictures },
            { headers: { Authorization: `Bearer ${token}` } }
        );

        console.log(`[SUCCESS] Cover updated! Status: ${updateRes.status}`);
        console.log(`New cover: ${updateRes.data.pictures[0].id}`);
    } catch (e) {
        console.error(`[ERROR] ${e.message}`);
        if (e.response) console.error(JSON.stringify(e.response.data, null, 2));
    }
}

uploadCover();
