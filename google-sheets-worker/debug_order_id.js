const fs = require('fs');
const axios = require('axios');

const INPUT_ID = '2000011335109665';

async function debugOrder() {
    try {
        const token = fs.readFileSync('ml_token.txt', 'utf8').trim();

        console.log(`Debug: Checking if ${INPUT_ID} is a Shipment...`);
        try {
            const shipRes = await axios.get(`https://api.mercadolibre.com/shipments/${INPUT_ID}`, {
                headers: { Authorization: `Bearer ${token}` }
            });
            console.log('✅ It IS a Shipment!');
            console.log('Associated Order ID:', shipRes.data.order_id);
            return;
        } catch (e) {
            console.log('Tested as Shipment: 404/Error');
        }

        console.log(`\nDebug: Checking if ${INPUT_ID} is a Payment...`);
        try {
            const payRes = await axios.get(`https://api.mercadolibre.com/payments/${INPUT_ID}`, {
                headers: { Authorization: `Bearer ${token}` }
            });
            console.log('✅ It IS a Payment!');
            console.log('Associated Order ID:', payRes.data.order_id);
            return;
        } catch (e) {
            console.log('Tested as Payment: 404/Error');
        }

        console.log('\n❌ Could not identify this ID type via direct lookup.');

    } catch (e) {
        console.error('Fatal:', e.message);
    }
}

debugOrder();
