const axios = require('axios');
const fs = require('fs');

async function checkPromotions() {
    try {
        const token = fs.readFileSync('ml_token.txt', 'utf8').trim();
        const itemId = 'MLA876778105';

        // Check available promotions
        // Endpoint: /seller-promotions/items/MLA876778105?app_version=v2

        try {
            const res = await axios.get(`https://api.mercadolibre.com/seller-promotions/items/${itemId}?app_version=v2`, {
                headers: { Authorization: `Bearer ${token}` }
            });
            console.log("\n--- Available Promotions ---");
            if (res.data && res.data.length > 0) {
                res.data.forEach(p => {
                    console.log(`- ID: ${p.id} | Type: ${p.type} | Status: ${p.status} | Name: ${p.name}`);
                    if (p.deadline_date) console.log(`  Deadline: ${p.deadline_date}`);
                    if (p.start_date) console.log(`  Start: ${p.start_date}`);
                    if (p.finish_date) console.log(`  End: ${p.finish_date}`);
                });
            } else {
                console.log("No promotions available for this item.");
            }
        } catch (e) {
            console.error("Error checking promotions:", e.message);
            if (e.response) console.log(JSON.stringify(e.response.data, null, 2));
        }

    } catch (e) {
        console.error(e.message);
    }
}

checkPromotions();
