const fs = require('fs');
const axios = require('axios');
const path = require('path');

const ITEM_ID = 'MLA801057163';

async function verifyCover() {
    try {
        const token = fs.readFileSync('ml_token.txt', 'utf8').trim();
        const dir = path.join(__dirname, 'verify_cover');
        if (!fs.existsSync(dir)) fs.mkdirSync(dir);

        console.log(`Fetching ${ITEM_ID}...`);

        const res = await axios.get(`https://api.mercadolibre.com/items/${ITEM_ID}`, {
            headers: { Authorization: `Bearer ${token}` }
        });

        const item = res.data;
        const pictures = item.pictures;

        console.log(`Total Pictures: ${pictures.length}`);

        if (pictures.length > 0) {
            const cover = pictures[0];
            console.log(`Cover ID: ${cover.id}`);
            console.log(`Cover URL: ${cover.url}`);

            const writer = fs.createWriteStream(path.join(dir, `${ITEM_ID}_check.jpg`));
            const imgRes = await axios({
                url: cover.url,
                method: 'GET',
                responseType: 'stream'
            });

            imgRes.data.pipe(writer);

            await new Promise((resolve, reject) => {
                writer.on('finish', resolve);
                writer.on('error', reject);
            });
            console.log('Cover downloaded.');
        } else {
            console.log('No pictures found.');
        }

    } catch (e) {
        console.error('Error:', e.message);
    }
}

verifyCover();
