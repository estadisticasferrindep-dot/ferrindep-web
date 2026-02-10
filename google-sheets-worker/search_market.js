const axios = require('axios');
const fs = require('fs');

async function searchMarket() {
    try {
        const query = "Malla Electrosoldada";
        console.log(`Searching for '${query}'...`);

        // Public endpoint, but using token is safer for limits
        // Note: tokens are for specific users, public search might behave differently with user token (personalized?). 
        // Using anonymous call usually gives standard ranking.
        // Let's try without token first to see "incognito" results, or with token if it fails.
        // Actually, user wants "donde estamos parados", implying standard buyer view.
        const token = fs.readFileSync('ml_token.txt', 'utf8').trim();
        const res = await axios.get(`https://api.mercadolibre.com/sites/MLA/search?q=${encodeURIComponent(query)}&limit=50`, {
            headers: { Authorization: `Bearer ${token}` }
        });

        const results = res.data.results || [];
        console.log(`Found ${results.length} results on Page 1.\n`);

        console.log(`| # | Vendedor | Precio | Título | Link |`);
        console.log(`|---|---|---|---|---|`);

        for (let i = 0; i < results.length; i++) {
            const item = results[i];
            const seller = item.seller;
            const nickname = seller.nickname || seller.id;
            const price = item.price.toLocaleString('es-AR', { style: 'currency', currency: 'ARS' });

            console.log(`| ${i + 1} | ${nickname} | ${price} | ${item.title} | [Ver](${item.permalink}) |`);
        }

    } catch (e) {
        console.error(`Error: ${e.message}`);
        if (e.response) console.error(JSON.stringify(e.response.data, null, 2));
    }
}

searchMarket();
