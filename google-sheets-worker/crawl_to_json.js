const axios = require('axios');
const cheerio = require('cheerio');
const fs = require('fs');

const BASE_URL = 'https://www.ferrindep.com.ar';
const queue = [BASE_URL, `${BASE_URL}/productos`]; // Start with both home and products
const products = [];
const visited = new Set();
const maxProducts = 200;

const clean = (text) => text ? text.replace(/\s+/g, ' ').trim() : '';

const generateKeywords = (name) => {
    if (!name) return [];
    const stopwords = ['de', 'la', 'el', 'en', 'para', 'con', 'y', 'mallas', 'malla'];
    const words = name.toLowerCase()
        .replace(/[^\w\s-]/gi, '')
        .split(' ')
        .filter(w => w.length > 2 && !stopwords.includes(w));

    // Extract dimensions like 50x50
    if (name.includes('x')) words.push(name.match(/\d+x\d+/)?.[0]);
    if (name.toLowerCase().includes('mm')) words.push('mm');

    return [...new Set(words.filter(w => w))];
};

async function crawl() {
    console.log(`🕷️ Crawling ${BASE_URL} (Max ${maxProducts} items)...`);

    while (queue.length > 0 && products.length < maxProducts) {
        const url = queue.shift();
        if (visited.has(url)) continue;
        visited.add(url);

        try {
            const { data } = await axios.get(url, { timeout: 10000 });
            const $ = cheerio.load(data);

            // LOGIC: Is it a product page?
            // Ferrindep seems to have /productos/slug or /producto/slug
            const isProduct = url.includes('/productos/') && !url.endsWith('/productos');

            if (isProduct) {
                // Try multiple selectors for the name
                const name = clean($('h1').text()) || clean($('.product-name').text()) || clean($('.page-title').text());

                if (name && name.length > 3) {
                    process.stdout.write('+'); // Found one
                    products.push({
                        keywords: generateKeywords(name),
                        name: name,
                        url: url
                    });
                }
            } else {
                process.stdout.write('.'); // Just browsing
            }

            // FIND LINKS
            $('a').each((i, el) => {
                const href = $(el).attr('href');
                if (!href) return;

                let full = href;
                if (href.startsWith('/')) full = BASE_URL + href;

                if (full.startsWith(BASE_URL) && !visited.has(full)) {
                    if (full.includes('/productos') || full.includes('/categorias')) {
                        queue.push(full);
                    }
                }
            });

            // Rate limit
            // await new Promise(r => setTimeout(r, 50));

        } catch (e) {
            // console.log(`x`);
        }
    }

    console.log(`\n\n✅ Done! Found ${products.length} products.`);
    fs.writeFileSync('products_db.json', JSON.stringify(products, null, 2));
    console.log('Saved to products_db.json');
}

crawl();
