const axios = require('axios');
const fs = require('fs');

async function removeWholesale() {
    const targets = ['MLA640050474', 'MLA2472352522', 'MLA801222637', 'MLA2305330562'];
    const token = fs.readFileSync('ml_token.txt', 'utf8').trim();

    for (const id of targets) {
        console.log(`Processing ${id}...`);
        try {
            // 1. Get Current Price
            const getRes = await axios.get(`https://api.mercadolibre.com/items/${id}`, {
                headers: { Authorization: `Bearer ${token}` }
            });
            const currentPrice = getRes.data.price;
            console.log(`   Current Price: $${currentPrice}`);

            // 2. Overwrite Pricing Scheme to Single Tier
            const payload = {
                pricing_scheme: {
                    prices: [
                        {
                            quantity: 1,
                            price: currentPrice
                        }
                    ]
                }
            };

            const putRes = await axios.put(`https://api.mercadolibre.com/items/${id}`, payload, {
                headers: { Authorization: `Bearer ${token}` }
            });

            console.log(`   [SUCCESS] Pricing Scheme Reset. Status: ${putRes.status}`);

            // Check if tag remains? (Optional)
            if (putRes.data.tags.includes('standard_price_by_quantity')) {
                console.log("   [INFO] Tag 'standard_price_by_quantity' still present (might update later).");
            } else {
                console.log("   [INFO] Tag 'standard_price_by_quantity' REMOVED.");
            }

        } catch (e) {
            console.error(`   [ERROR] Failed to update ${id}: ${e.message}`);
            if (e.response) console.error(JSON.stringify(e.response.data, null, 2));
        }
    }
}

removeWholesale();
