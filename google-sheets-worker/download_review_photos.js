const axios = require('axios');
const fs = require('fs');
const path = require('path');

async function downloadPhotos() {
    const itemId = 'MLA635449329';
    const token = fs.readFileSync('ml_token.txt', 'utf8').trim();
    const outDir = path.join(__dirname, 'downloaded_photos', itemId);

    if (!fs.existsSync(outDir)) fs.mkdirSync(outDir, { recursive: true });

    try {
        console.log(`Fetching reviews for ${itemId}...`);
        const res = await axios.get(`https://api.mercadolibre.com/reviews/item/${itemId}?limit=50`, {
            headers: { Authorization: `Bearer ${token}` }
        });

        const reviews = res.data.reviews || [];
        console.log(`Found ${reviews.length} reviews. Extracting photos...`);

        let count = 0;
        for (const r of reviews) {
            if (r.media && r.media.length > 0) {
                for (let i = 0; i < r.media.length; i++) {
                    const m = r.media[i];

                    // Logic to find best URL from variations
                    let url = null;
                    if (m.variations && m.variations.length > 0) {
                        // Pick the one with largest size? or just first?
                        // First one in the log was 1200x1068 (usually the original/biggest).
                        url = m.variations[0].url;
                    } else if (m.link) {
                        url = m.link;
                    }

                    if (url) {
                        const ext = '.jpg'; // Assume jpg
                        const filename = `review_${r.id}_${i + 1}${ext}`;
                        const filePath = path.join(outDir, filename);

                        console.log(`  Downloading ${url} -> ${filename}`);
                        await downloadImage(url, filePath);
                        count++;
                    }
                }
            }
        }

        console.log(`\n[SUCCESS] Downloaded ${count} photos to:\n${outDir}`);

    } catch (e) {
        console.error(`Error: ${e.message}`);
    }
}

async function downloadImage(url, filepath) {
    const writer = fs.createWriteStream(filepath);
    const response = await axios({
        url,
        method: 'GET',
        responseType: 'stream'
    });
    response.data.pipe(writer);
    return new Promise((resolve, reject) => {
        writer.on('finish', resolve);
        writer.on('error', reject);
    });
}

downloadPhotos();
