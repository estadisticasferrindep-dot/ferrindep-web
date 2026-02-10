const axios = require('axios');
const fs = require('fs');

const INPUT_DB = 'products_db.json';
const OUTPUT_DB = 'web_variations_db.json';

async function scrapeVariations() {
    try {
        console.log("🚀 Starting Web Scraper for Product Variations...");
        const products = JSON.parse(fs.readFileSync(INPUT_DB, 'utf8'));
        const variationsDB = [];

        // Concurrency Control (don't ddos the site)
        const BATCH_SIZE = 5;
        let processed = 0;

        for (let i = 0; i < products.length; i += BATCH_SIZE) {
            const batch = products.slice(i, i + BATCH_SIZE);
            const promises = batch.map(async (p) => {
                try {
                    // console.log(`Fetching ${p.url}...`);
                    const res = await axios.get(p.url, { timeout: 10000 });
                    const html = res.data;

                    // SCRAPING LOGIC
                    let height = "N/A";
                    let length = "N/A";

                    // Regex for Height (Altura)
                    // Matches: <td>Altura...</td> ... <td>...30cm...</td>
                    const heightMatch = html.match(/<td>Altura.*?<\/td>\s*<td>.*?<strong>(.*?)<\/strong>/s);
                    if (heightMatch) {
                        // Clean tags like <span> or &nbsp;
                        height = heightMatch[1].replace(/<[^>]+>/g, '').replace(/&nbsp;/g, ' ').trim();
                    }

                    // Regex for Length (Presentación/Longitud)
                    // Matches: <td>Presentación...</td> ... <td>...20 metros...</td>
                    const lengthMatch = html.match(/<td>Presentación.*?<\/td>\s*<td>.*?<strong>(.*?)<\/strong>/s);
                    if (lengthMatch) {
                        length = lengthMatch[1].replace(/<[^>]+>/g, '').replace(/&nbsp;/g, ' ').trim();
                    }

                    return {
                        id: p.url.split('/').pop(), // Product ID from URL
                        name: p.name,
                        url: p.url,
                        height: height,
                        length: length,
                        full_spec: `${p.name} - Alto: ${height} - Largo: ${length}`
                    };

                } catch (err) {
                    console.error(`❌ Failed to fetch ${p.url}: ${err.message}`);
                    return null;
                }
            });

            const results = await Promise.all(promises);
            results.forEach(r => {
                if (r) variationsDB.push(r);
            });

            processed += batch.length;
            process.stdout.write(`...Scraped ${processed} / ${products.length} products\r`);
        }

        console.log(`\n💾 Saving ${variationsDB.length} variations to ${OUTPUT_DB}...`);
        fs.writeFileSync(OUTPUT_DB, JSON.stringify(variationsDB, null, 2));
        console.log("✅ Done!");

    } catch (e) {
        console.error("Critical Error:", e);
    }
}

scrapeVariations();
