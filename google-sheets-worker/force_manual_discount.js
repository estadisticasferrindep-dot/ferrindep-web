const fs = require('fs');
const axios = require('axios');

const ITEM_ID = 'MLA1427437453';
const NEW_PRICE = 5100;
const OLD_PRICE = 5392;

async function forceManualDiscount() {
    try {
        const token = fs.readFileSync('ml_token.txt', 'utf8').trim();
        if (!token) throw new Error('No token found in ml_token.txt');

        console.log(`Forcing Manual Discount on ${ITEM_ID}...`);
        console.log(`Setting Price: ${NEW_PRICE} (was ${OLD_PRICE})`);
        console.log(`Setting Original Price: ${OLD_PRICE}`);

        const url = `https://api.mercadolibre.com/items/${ITEM_ID}`;

        try {
            const res = await axios.put(url, {
                price: NEW_PRICE,
                original_price: OLD_PRICE
            }, {
                headers: { Authorization: `Bearer ${token}` }
            });

            console.log('SUCCESS! Manual Price Updated.');
            console.log(`New Title: ${res.data.title}`);
            console.log(`New Price: ${res.data.price}`);
            console.log(`New Original Price: ${res.data.original_price}`);
            console.log(`Permalink: ${res.data.permalink}`);

        } catch (apiErr) {
            console.error('API Error:', apiErr.response ? apiErr.response.data : apiErr.message);
        }

    } catch (e) {
        console.error('Error:', e.message);
    }
}

forceManualDiscount();
