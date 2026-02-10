const axios = require('axios');
const fs = require('fs');

async function checkItem() {
    try {
        const token = fs.readFileSync('ml_token.txt', 'utf8').trim();
        const itemId = 'MLA876778105';

        const res = await axios.get(`https://api.mercadolibre.com/items/${itemId}`, {
            headers: { Authorization: `Bearer ${token}` }
        });

        const item = res.data;
        console.log(`Title: ${item.title}`);
        console.log(`Price: ${item.price}`); // Current Price
        console.log(`Base Price: ${item.base_price}`);
        console.log(`Original Price: ${item.original_price}`); // If older discount exists
        console.log(`Status: ${item.status}`);
        console.log(`Listing Type: ${item.listing_type_id}`);
        console.log(`Permalink: ${item.permalink}`);

    } catch (e) {
        console.error(e.message);
    }
}

checkItem();
