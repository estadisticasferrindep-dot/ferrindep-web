const axios = require('axios');
const fs = require('fs');

async function removeTags() {
    const targets = ['MLA640050474', 'MLA2472352522', 'MLA801222637', 'MLA2305330562'];
    const token = fs.readFileSync('ml_token.txt', 'utf8').trim();

    for (const id of targets) {
        console.log(`Processing ${id}...`);
        try {
            // 1. Get Current Tags
            const getRes = await axios.get(`https://api.mercadolibre.com/items/${id}`, {
                headers: { Authorization: `Bearer ${token}` }
            });
            const currentTags = getRes.data.tags || [];

            if (!currentTags.includes('standard_price_by_quantity')) {
                console.log(`   Tag 'standard_price_by_quantity' not found on ${id}.`);
                continue;
            }

            const newTags = currentTags.filter(t => t !== 'standard_price_by_quantity');
            console.log(`   Removing tag. Old Count: ${currentTags.length}, New Count: ${newTags.length}`);

            // 2. PUT Update
            const res = await axios.put(`https://api.mercadolibre.com/items/${id}`, { tags: newTags }, {
                headers: { Authorization: `Bearer ${token}` }
            });

            console.log("   [SUCCESS] Tag removed.");
            // console.log("   New Tags:", res.data.tags);

        } catch (e) {
            console.error(`   [ERROR] Failed to update ${id}: ${e.message}`);
            if (e.response) {
                console.error(`   API Error: ${e.response.status} - ${JSON.stringify(e.response.data)}`);
            }
        }
    }
}

removeTags();
