const axios = require('axios');
const fs = require('fs');

const QUERY = 'malla electrosoldada 10x10mm 19cm canaleta';

async function searchCompetitors() {
    try {
        const token = fs.readFileSync('ml_token.txt', 'utf8').trim();
        console.log(`Searching for: "${QUERY}"...`);

        const res = await axios.get('https://api.mercadolibre.com/sites/MLA/search', {
            headers: {
                'Authorization': `Bearer ${token}`,
                'User-Agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36'
            },
            params: {
                q: QUERY,
                limit: 10,
                sort: 'price_asc'
            }
        });

        const items = res.data.results;

        if (items.length === 0) {
            console.log('No results found.');
            return;
        }

        console.log(`Found ${items.length} items:\n`);

        items.forEach((item, i) => {
            console.log(`#${i + 1} [${item.id}] $${item.price}`);
            console.log(`Title: ${item.title}`);
            console.log(`Seller: ${item.seller.nickname} (ID: ${item.seller.id})`);
            console.log(`Link: ${item.permalink}`);
            console.log('---');
        });

    } catch (e) {
        console.error('Error:', e.message);
        console.error('Data:', e.response ? JSON.stringify(e.response.data) : 'No Data');
    }
}

searchCompetitors();
