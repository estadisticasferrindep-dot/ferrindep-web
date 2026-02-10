const fs = require('fs');
const axios = require('axios');
const path = require('path');

const ITEMS = [
    'MLA823046775', 'MLA1130295109', 'MLA1130269121', 'MLA801581076',
    'MLA1508190538', 'MLA1130275804', 'MLA1130282299', 'MLA801582337',
    'MLA801222637', 'MLA2534510664', 'MLA1141811979', 'MLA801574713',
    'MLA1141824385', 'MLA801225506', 'MLA801057163', 'MLA1130308329'
];

async function downloadImages() {
    try {
        const token = fs.readFileSync('ml_token.txt', 'utf8').trim();
        const dir = path.join(__dirname, 'cover_audit');
        if (!fs.existsSync(dir)) fs.mkdirSync(dir);

        console.log(`Fetching 16 items...`);

        // We can use multiget: /items?ids=...
        const idsString = ITEMS.join(',');
        const res = await axios.get(`https://api.mercadolibre.com/items?ids=${idsString}`, {
            headers: { Authorization: `Bearer ${token}` }
        });

        const results = res.data;

        for (const itemData of results) {
            const item = itemData.body;
            if (itemData.code !== 200) {
                console.error(`Error fetching ${itemData.id}`);
                continue;
            }

            const coverId = item.thumbnail_id || item.pictures[0].id;
            // thumbnail usually is smaller (http://http2.mlstatic.com/D_681966-MLA...-O.jpg)
            // item.thumbnail is the URL.
            // item.secure_thumbnail is HTTPS.
            // But pictures[0].url is better quality (500x500 or max).

            const pic = item.pictures.find(p => p.id === coverId) || item.pictures[0];
            const url = pic.url; // url is usually max resolution available in 'pictures' array

            console.log(`Downloading ${item.id}: ${url}`);

            const writer = fs.createWriteStream(path.join(dir, `${item.id}.jpg`));
            const imgRes = await axios({
                url,
                method: 'GET',
                responseType: 'stream'
            });

            imgRes.data.pipe(writer);

            await new Promise((resolve, reject) => {
                writer.on('finish', resolve);
                writer.on('error', reject);
            });
        }
        console.log('All images downloaded to /cover_audit/');

    } catch (e) {
        console.error('Error:', e.message);
    }
}

downloadImages();
