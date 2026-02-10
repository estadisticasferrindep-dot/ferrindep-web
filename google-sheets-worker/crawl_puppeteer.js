const puppeteer = require('puppeteer');
const fs = require('fs');

const BASE_URL = 'https://www.ferrindep.com.ar';
const queue = [BASE_URL];
const products = [];
const visited = new Set();
const maxProducts = 300; // Increased limit

const generateKeywords = (name) => {
    if (!name) return [];
    const stopwords = ['de', 'la', 'el', 'en', 'para', 'con', 'y', 'malla', 'mallas', 'rollo', 'panel'];
    const words = name.toLowerCase()
        .replace(/[^\w\s-]/gi, '')
        .split(' ')
        .filter(w => w.length > 2 && !stopwords.includes(w));

    // Explicitly add dimension variations
    // "10x10" -> "10x10", "10x10mm"
    const dimensions = name.match(/\d+\s?[xX]\s?\d+/g);
    if (dimensions) {
        dimensions.forEach(d => {
            words.push(d.replace(/\s/g, '').toLowerCase());
            words.push(d.replace(/\s/g, '').toLowerCase() + 'mm');
        });
    }

    return [...new Set(words)];
};

(async () => {
    console.log('🕷️ Spider-Bot Active. Learning Ferrindep catalog...');
    const browser = await puppeteer.launch({
        headless: "new",
        args: ['--no-sandbox', '--disable-setuid-sandbox']
    });
    const page = await browser.newPage();

    // Desktop Viewport
    await page.setViewport({ width: 1280, height: 800 });

    while (queue.length > 0 && products.length < maxProducts) {
        const url = queue.shift();
        if (visited.has(url)) continue;
        visited.add(url);

        try {
            await page.goto(url, { waitUntil: 'domcontentloaded', timeout: 10000 });

            // Heuristic for Product Page: URL contains "/producto/"
            if (url.includes('/producto/')) {
                // Try multiple selectors common in Laravel Blade templates
                const titleSelector = await page.evaluate(() => {
                    const h1 = document.querySelector('h1');
                    if (h1) return h1.innerText;

                    const pName = document.querySelector('.product-name');
                    if (pName) return pName.innerText;

                    // Fallback: title tag
                    return document.title.split('|')[0].split('-')[0];
                });

                if (titleSelector && titleSelector.length > 3 && !titleSelector.includes('404')) {
                    process.stdout.write('📦'); // Package icon for product
                    products.push({
                        keywords: generateKeywords(titleSelector),
                        name: titleSelector.trim(),
                        url: url
                    });
                }
            } else {
                process.stdout.write('.'); // Just browsing
            }

            // Harvest Links (BFS)
            const hrefs = await page.$$eval('a', as => as.map(a => a.href));
            for (const href of hrefs) {
                if (href.startsWith(BASE_URL) && !visited.has(href)) {
                    // Prioritize Products and Categories
                    if (href.includes('/producto/') || href.includes('/productos/') || href.includes('/categoria/')) {
                        queue.push(href);
                    }
                }
            }

        } catch (e) {
            // console.log('x');
        }
    }

    console.log(`\n\n✅ Learning Complete.`);
    console.log(`🧠 Memorized ${products.length} products.`);

    // Fallback: If 0 products, try to restore backup? (Not implemented, user has to re-generate)
    if (products.length > 0) {
        fs.writeFileSync('products_db.json', JSON.stringify(products, null, 2));
    } else {
        console.log("⚠️ No products found. Checking homepage layout...");
    }

    await browser.close();
})();
