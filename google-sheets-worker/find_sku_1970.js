const axios = require('axios');
const fs = require('fs');

async function findItem() {
    try {
        const token = fs.readFileSync('ml_token.txt', 'utf8').trim();
        const userId = '97128565';
        const sku = '1970';

        const res = await axios.get(`https://api.mercadolibre.com/users/${userId}/items/search?seller_sku=${sku}`, {
            headers: { Authorization: `Bearer ${token}` }
        });

        if (res.data.results && res.data.results.length > 0) {
            console.log(`Found Item ID: ${res.data.results[0]}`);
            return res.data.results[0];
        } else {
            console.log("SKU not found via API search. Trying Sheet search...");
            // Fallback to sheet search if needed, but API usually works for active items.
        }
    } catch (e) {
        console.error(e.message);
    }
}

findItem();
