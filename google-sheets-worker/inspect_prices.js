const axios = require('axios');
const fs = require('fs');

async function inspectPrices() {
    const id = 'MLA2472352522';
    const token = fs.readFileSync('ml_token.txt', 'utf8').trim();

    try {
        console.log(`Getting prices for ${id}...`);
        const res = await axios.get(`https://api.mercadolibre.com/items/${id}/prices`, {
            headers: { Authorization: `Bearer ${token}` }
        });
        console.log(JSON.stringify(res.data, null, 2));

    } catch (e) {
        console.error(`Error: ${e.message}`);
        if (e.response) console.error(JSON.stringify(e.response.data, null, 2));
    }
}

inspectPrices();
