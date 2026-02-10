const axios = require('axios');
const fs = require('fs');
const path = require('path');

async function downloadCover() {
    const token = fs.readFileSync('ml_token.txt', 'utf8').trim();
    const itemId = 'MLA1392416857';

    const res = await axios.get(`https://api.mercadolibre.com/items/${itemId}`, {
        headers: { Authorization: `Bearer ${token}` }
    });

    const pictures = res.data.pictures || [];
    console.log(`Title: ${res.data.title}`);
    console.log(`Total pictures: ${pictures.length}`);

    if (pictures.length > 0) {
        const cover = pictures[0];
        // Use full size URL (replace -O with -F for full)
        const fullUrl = cover.secure_url.replace('-O.jpg', '-F.jpg');
        console.log(`Cover ID: ${cover.id}`);
        console.log(`Cover URL: ${fullUrl}`);

        const outDir = path.join(__dirname, 'fix_watermark');
        if (!fs.existsSync(outDir)) fs.mkdirSync(outDir, { recursive: true });

        const imgRes = await axios({ url: fullUrl, method: 'GET', responseType: 'stream' });
        const filePath = path.join(outDir, `cover_${itemId}.jpg`);
        const writer = fs.createWriteStream(filePath);
        imgRes.data.pipe(writer);

        await new Promise((resolve, reject) => {
            writer.on('finish', resolve);
            writer.on('error', reject);
        });

        console.log(`[SUCCESS] Downloaded to: ${filePath}`);
    }
}

downloadCover();
