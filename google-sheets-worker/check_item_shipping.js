const axios = require('axios');
const fs = require('fs');

async function checkItemShipping() {
    const token = fs.readFileSync('ml_token.txt', 'utf8').trim();
    const itemId = 'MLA901674689';

    try {
        const res = await axios.get(`https://api.mercadolibre.com/items/${itemId}`, {
            headers: { Authorization: `Bearer ${token}` }
        });
        const item = res.data;

        console.log(`Title: ${item.title}`);
        console.log(`Available Quantity: ${item.available_quantity}`);
        console.log(`Sale Terms:`, JSON.stringify(item.sale_terms, null, 2));
        console.log(`Shipping:`, JSON.stringify(item.shipping, null, 2));

    } catch (e) {
        console.error(`Error: ${e.message}`);
    }
}

checkItemShipping();
