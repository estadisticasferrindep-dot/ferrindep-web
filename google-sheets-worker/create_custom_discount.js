const axios = require('axios');
const fs = require('fs');

async function createDiscount() {
    try {
        const token = fs.readFileSync('ml_token.txt', 'utf8').trim();
        const itemId = 'MLA819860485'; // SKU 1565
        const currentPrice = 197612; // From simulation
        const discountPercentage = 0.05;
        const dealPrice = Math.floor(currentPrice * (1 - discountPercentage));
        // Let's use 11637.

        // Dates
        const now = new Date();
        const endDate = new Date();
        endDate.setDate(now.getDate() + 3);

        const payload = {
            promotion_type: 'PRICE_DISCOUNT',
            deal_price: dealPrice,
            // Try format YYYY-MM-DDTHH:MM:SS
            start_date: now.toISOString().split('.')[0], // Removes milliseconds and Z
            finish_date: endDate.toISOString().split('.')[0],
            original_price: currentPrice,
        };

        console.log("Creating Promotion:", payload);

        // Endpoint: /seller-promotions/items/{ITEM_ID}?app_version=v2
        // Method: POST? Or PUT?
        // Usually creating a new custom offer is not via POST /seller-promotions/items/ID directly unless it's a specific campaign.
        // For 'PRICE_DISCOUNT' (custom offer), we usually use:
        // POST /items/{ITEM_ID}/prices or similar?
        // Wait, documentation says:
        // To create a traditional discount:
        // POST /seller-promotions/items/{ITEM_ID}
        // Body: { promotion_type: 'PRICE_DISCOUNT', deal_price: X, start_date: Y, finish_date: Z }

        try {
            const res = await axios.post(`https://api.mercadolibre.com/seller-promotions/items/${itemId}?app_version=v2`, payload, {
                headers: { Authorization: `Bearer ${token}` }
            });
            console.log("Success!", res.data);
        } catch (e) {
            console.error("Error creating promotion:", e.message);
            if (e.response) console.log(JSON.stringify(e.response.data, null, 2));
        }

    } catch (e) {
        console.error(e.message);
    }
}

createDiscount();
