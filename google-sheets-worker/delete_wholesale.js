const axios = require('axios');
const fs = require('fs');

async function deletePricingScheme() {
    const id = 'MLA640050474';
    const token = fs.readFileSync('ml_token.txt', 'utf8').trim();

    try {
        console.log(`Deleting pricing_scheme for ${id}...`);
        // Try direct DELETE on the property? Unlikely to work as URL but worth a shot if documented? 
        // No, usually it's PUT with null or empty, or specific endpoint.
        // Let's try PUT with null first as previous attempt was PUT with single price.

        const payload = {
            pricing_scheme: null // Explicit null
        };

        // Also try DELETE method on standard URL? No, that deletes Item.
        // Maybe DELETE /items/{id}/pricing_scheme exists?

        try {
            await axios.delete(`https://api.mercadolibre.com/items/${id}/pricing_scheme`, {
                headers: { Authorization: `Bearer ${token}` }
            });
            console.log("DELETE endpoint worked!");
            return;
        } catch (e) { console.log("DELETE endpoint failed: " + e.response?.status); }

        // Try PUT with null
        console.log("Trying PUT with properties: null...");
        const res = await axios.put(`https://api.mercadolibre.com/items/${id}`, { pricing_scheme: null }, {
            headers: { Authorization: `Bearer ${token}` }
        });
        console.log("PUT null success!", res.status);

    } catch (e) {
        console.error(`Error: ${e.message}`);
        if (e.response) console.error(JSON.stringify(e.response.data, null, 2));
    }
}

deletePricingScheme();
