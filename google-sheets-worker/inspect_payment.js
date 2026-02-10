const axios = require('axios');
const fs = require('fs');

async function inspectPayment() {
    try {
        const token = fs.readFileSync('ml_token.txt', 'utf8').trim();
        const paymentId = '145250325848';

        try {
            const res = await axios.get(`https://api.mercadolibre.com/collections/${paymentId}`, {
                headers: { Authorization: `Bearer ${token}` }
            });
            console.log("\n--- FULL COLLECTION OBJECT ---");
            console.log(JSON.stringify(res.data, null, 2));

        } catch (e) {
            console.error("Error fetching payment:", e.message);
            if (e.response) console.log(e.response.data);
        }
    } catch (e) { console.error(e); }
}

inspectPayment();
