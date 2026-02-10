const axios = require('axios');
const fs = require('fs');

async function inspectReviews() {
    const id = 'MLA640050474';
    const token = fs.readFileSync('ml_token.txt', 'utf8').trim();

    try {
        console.log(`Fetching reviews for ${id}...`);
        // Endpoint for reviews: /reviews/item/{itemId} 
        // Note: verify if this endpoint is public/accessible with seller token
        const res = await axios.get(`https://api.mercadolibre.com/reviews/item/${id}?limit=5`, {
            headers: { Authorization: `Bearer ${token}` }
        });

        console.log("Reviews found:", res.data.paging.total);
        if (res.data.reviews && res.data.reviews.length > 0) {
            console.log("Sample Review:", JSON.stringify(res.data.reviews[0], null, 2));
        } else {
            console.log("No reviews found for this item.");
        }

    } catch (e) {
        console.error(`Error: ${e.message}`);
        if (e.response) console.error(JSON.stringify(e.response.data, null, 2));
    }
}

inspectReviews();
