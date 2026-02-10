const axios = require('axios');
const fs = require('fs');

async function scrapeMarket() {
    try {
        const url = 'https://listado.mercadolibre.com.ar/malla-electrosoldada';
        console.log(`Scraping ${url}...`);

        const res = await axios.get(url, {
            headers: {
                'User-Agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
                'Accept': 'text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8',
            }
        });

        const html = res.data;
        // console.log(html.substring(0, 500)); 

        // Regex to find items (very fragile, but better than nothing)
        // ML items usually in <li class="ui-search-layout__item">
        // Titles in <h2 class="ui-search-item__title">
        // Prices in <span class="andes-money-amount__fraction">
        // Sellers often hidden or in "por [Seller]" text.

        const items = [];

        // Split by item container
        const parts = html.split('ui-search-layout__item');

        console.log(`| # | Título | Precio | Link |`);
        console.log(`|---|---|---|---|`);

        let count = 0;
        for (let i = 1; i < parts.length; i++) {
            const part = parts[i];

            // Title
            const titleMatch = part.match(/ui-search-item__title">(.*?)<\/h/);
            const title = titleMatch ? titleMatch[1] : 'N/A';

            // Price (First occurrence usually main price)
            const priceMatch = part.match(/andes-money-amount__fraction">(.*?)<\/span/);
            const price = priceMatch ? priceMatch[1] : 'N/A';

            // Link
            const linkMatch = part.match(/href="(.*?)"/);
            const link = linkMatch ? linkMatch[1] : 'N/A';

            // Seller (Hard to find in list view often)
            // Sometimes "por SellerName"
            const sellerMatch = part.match(/class="ui-search-official-store-label".*?>(.*?)<\/p/); // Official stores
            // Or look for "por " logic which varies.

            if (title !== 'N/A') {
                count++;
                console.log(`| ${count} | ${title} | $${price} | [Ver](${link}) |`);
                if (count >= 20) break;
            }
        }

    } catch (e) {
        console.error(`Error: ${e.message}`);
    }
}

scrapeMarket();
