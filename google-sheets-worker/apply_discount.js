const fs = require('fs');
const axios = require('axios');

const ITEM_ID = 'MLA1427437453';
const DISCOUNT_PRICE = 5000; // Try deeper discount (~7%)

async function applyDiscount() {
    try {
        const token = fs.readFileSync('ml_token.txt', 'utf8').trim();
        if (!token) throw new Error('No token found in ml_token.txt');

        console.log(`Applying 5% Discount to ${ITEM_ID}...Target Price: ${DISCOUNT_PRICE}`);

        const url = `https://api.mercadolibre.com/seller-promotions/items/${ITEM_ID}`;

        const today = new Date();
        const nextWeek = new Date();
        nextWeek.setDate(today.getDate() + 7);

        // Manual format YYYY-MM-DDTHH:mm:ss
        const toLocalIso = (d) => {
            const pad = (n) => n < 10 ? '0' + n : n;
            return d.getFullYear() + '-' +
                pad(d.getMonth() + 1) + '-' +
                pad(d.getDate()) + 'T' +
                pad(d.getHours()) + ':' +
                pad(d.getMinutes()) + ':' +
                pad(d.getSeconds());
        };

        const start = toLocalIso(today);
        const finish = toLocalIso(nextWeek);

        console.log(`Dates: ${start} to ${finish}`);

        try {
            const res = await axios.post(url, {
                promotion_type: 'PRICE_DISCOUNT',
                deal_price: DISCOUNT_PRICE,
                start_date: start,
                finish_date: finish
            }, {
                headers: { Authorization: `Bearer ${token}` },
                params: { app_version: 'v2' }
            });

            console.log('SUCCESS! Discount Applied.');
            console.log('Response:', JSON.stringify(res.data, null, 2));

        } catch (apiErr) {
            console.error('API Error:', apiErr.response ? apiErr.response.data : apiErr.message);
        }

    } catch (e) {
        console.error('Error:', e.message);
    }
}

applyDiscount();
