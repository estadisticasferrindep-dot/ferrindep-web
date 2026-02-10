const axios = require('axios');
const fs = require('fs');

async function inspectItem() {
    try {
        const token = fs.readFileSync('ml_token.txt', 'utf8').trim();
        const itemId = 'MLA640050474';

        const res = await axios.get(`https://api.mercadolibre.com/items/${itemId}`, {
            headers: { Authorization: `Bearer ${token}` }
        });

        console.log("--- Full Item JSON ---");
        console.log(JSON.stringify(res.data, null, 2));

        // Let's also check /prices specifically just in case
        /*
        try {
            const pricesRes = await axios.get(`https://api.mercadolibre.com/items/${itemId}/prices`, { // This endpoint might require specific permissions or show internal repr
                 headers: { Authorization: `Bearer ${token}` }
            });
            console.log("\n--- Prices Endpoint ---");
            console.log(JSON.stringify(pricesRes.data, null, 2));
        } catch(e) { console.log("Prices endpoint failed: " + e.message); }
        */

    } catch (e) {
        console.error(e.message);
    }
}

inspectItem();
