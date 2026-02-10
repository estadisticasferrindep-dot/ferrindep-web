const puppeteer = require('puppeteer');

(async () => {
    const browser = await puppeteer.launch({
        headless: "new",
        args: ['--no-sandbox']
    });
    const page = await browser.newPage();
    const target = 'https://www.ferrindep.com.ar';

    console.log(`🔍 Inspecting Home: ${target}`);

    try {
        await page.goto(target, { waitUntil: 'networkidle2', timeout: 20000 });

        const title = await page.title();
        console.log(`Title: ${title}`);

        // Get all links
        const hrefs = await page.$$eval('a', as => as.map(a => a.href));
        console.log(`Found ${hrefs.length} links.`);

        // Filter interesting ones
        const interesting = hrefs.filter(h => h.includes('product') || h.includes('categor'));
        console.log("--- Interesting Links ---");
        interesting.slice(0, 10).forEach(l => console.log(l));

        if (interesting.length === 0) {
            console.log("No obvious product links. Dumping first 10 links:");
            hrefs.slice(0, 10).forEach(l => console.log(l));
        }

    } catch (e) {
        console.error("Error:", e.message);
    }

    await browser.close();
})();
