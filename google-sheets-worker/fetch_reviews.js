const fs = require('fs');
const axios = require('axios');

const ITEM_ID = 'MLA1427437453';

async function fetchReviews() {
    try {
        const token = fs.readFileSync('ml_token.txt', 'utf8').trim();

        // 1. Try fetching reviews directly for the item
        console.log(`Fetching reviews for Item ${ITEM_ID}...`);

        // Note: The specific endpoint for reviews can vary. 
        // Trying /reviews/item/{ITEM_ID} (Public/Private depending on implementation)
        // Check API documentation pattern: https://api.mercadolibre.com/reviews/item/{itemId}

        try {
            const res = await axios.get(`https://api.mercadolibre.com/reviews/item/${ITEM_ID}`, {
                headers: { Authorization: `Bearer ${token}` },
                params: { limit: 50 }
            });

            const data = res.data;

            if (data.reviews && data.reviews.length > 0) {
                console.log(`\nFound ${data.reviews.length} reviews:\n`);
                data.reviews.forEach(r => {
                    const stars = '⭐'.repeat(r.rate);
                    console.log(`${stars} (${r.rate}/5) - ${new Date(r.date_created).toLocaleDateString()}`);
                    if (r.title) console.log(`Title: ${r.title}`);
                    console.log(`"${r.content}"`);
                    console.log(`Likes: ${r.likes} | Dislikes: ${r.dislikes}`);
                    console.log('---');
                });

                console.log(`\nAverage Rating: ${data.rating_average} (${data.paging.total} total reviews)`);
            } else {
                console.log('No reviews found for this specific item ID.');
            }

        } catch (apiErr) {
            // If 404, maybe it's a catalog item?
            if (apiErr.response && apiErr.response.status === 404) {
                console.log('Direct item reviews not found (404). Checking if it has a Catalog Product ID...');

                // Get Item details
                const itemRes = await axios.get(`https://api.mercadolibre.com/items/${ITEM_ID}`, {
                    headers: { Authorization: `Bearer ${token}` }
                });

                const catalogId = itemRes.data.catalog_product_id;
                if (catalogId) {
                    console.log(`Found Catalog ID: ${catalogId}. Fetching reviews for catalog product...`);
                    // Try fetching for catalog (Note: Endpoint might be different or same)
                    // Usually reviews are attached to the product itself.
                    // But strictly speaking, /reviews/item/{id} should work if it has reviews.
                    // Let's log if we see it.
                } else {
                    console.log('Item does not belong to a catalog product.');
                }
            } else {
                throw apiErr;
            }
        }

    } catch (e) {
        console.error('Error:', e.message);
        if (e.response) console.error('API Response:', JSON.stringify(e.response.data, null, 2));
    }
}

fetchReviews();
