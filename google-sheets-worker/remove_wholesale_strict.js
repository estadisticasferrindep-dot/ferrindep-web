const axios = require('axios');
const fs = require('fs');

async function removeTagStrict() {
    const id = 'MLA2472352522';
    const token = fs.readFileSync('ml_token.txt', 'utf8').trim();

    try {
        console.log(`Processing ${id}...`);

        // 1. Get Current Tags
        const getRes = await axios.get(`https://api.mercadolibre.com/items/${id}`, {
            headers: { Authorization: `Bearer ${token}` }
        });
        const currentTags = getRes.data.tags || [];

        if (!currentTags.includes('standard_price_by_quantity')) {
            console.log("Tag 'standard_price_by_quantity' ALREADY GONE.");
            return;
        }

        const newTags = currentTags.filter(t => t !== 'standard_price_by_quantity');
        console.log(`Removing tag. Old Count: ${currentTags.length}, New Count: ${newTags.length}`);

        // 2. PUT Update
        const res = await axios.put(`https://api.mercadolibre.com/items/${id}`, { tags: newTags }, {
            headers: { Authorization: `Bearer ${token}` }
        });

        console.log("PUT Status:", res.status); // Should be 200
        console.log("Returned Tags:", res.data.tags); // Check if it's gone

        if (res.data.tags.includes('standard_price_by_quantity')) {
            console.log("[FAIL] Tag Persisted despite PUT success.");
        } else {
            console.log("[SUCCESS] Tag Removed.");
        }

    } catch (e) {
        console.error(`Error: ${e.message}`);
        if (e.response) console.error(JSON.stringify(e.response.data, null, 2));
    }
}

removeTagStrict();
