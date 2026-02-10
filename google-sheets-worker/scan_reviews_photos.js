const axios = require('axios');
const fs = require('fs');

async function scanReviewsForPhotos() {
    const token = fs.readFileSync('ml_token.txt', 'utf8').trim();
    const userId = '97128565';

    // 1. Fetch All Active Items (using scroll id pattern or just iterate stored list if I had it)
    let allIds = [];
    let scrollId = null;

    console.log("Fetching IDs...");
    try {
        while (true) {
            let url = `https://api.mercadolibre.com/users/${userId}/items/search?search_type=scan&status=active&limit=100`;
            if (scrollId) url += `&scroll_id=${scrollId}`;
            const res = await axios.get(url, { headers: { Authorization: `Bearer ${token}` } });
            const results = res.data.results || [];
            if (results.length === 0) break;
            allIds = allIds.concat(results);
            scrollId = res.data.scroll_id;
            process.stdout.write(`\rFound ${allIds.length}`);
        }
    } catch (e) { }
    console.log(`\nScanning ${allIds.length} items for Reviews with Photos...`);

    // 2. Scan Reviews
    // We can't batch review calls. It's 1 call per item.
    // To be fast, maybe parallelize with limit? Or simple sequential.
    // 1200 calls might take ~5 mins if sequential.

    let found = [];
    let count = 0;

    for (const id of allIds) {
        count++;
        // process.stdout.write(`\rChecking ${count}/${allIds.length}: ${id}`);
        try {
            const res = await axios.get(`https://api.mercadolibre.com/reviews/item/${id}?limit=50`, { // Limit 50 reviews to check
                headers: { Authorization: `Bearer ${token}` }
            });

            const reviews = res.data.reviews || [];
            let hasPhoto = false;
            let photoCount = 0;

            for (const r of reviews) {
                if (r.media && r.media.length > 0) {
                    hasPhoto = true;
                    photoCount += r.media.length;
                    // found.push({ id, reviewId: r.id, photo: r.media[0] }); 
                }
            }

            if (hasPhoto) {
                // Get title for report
                // We don't have title here, need to fetch item? 
                // Delay title fetch to the end to save calls? Or just report ID.
                found.push({ id, photoCount });
                process.stdout.write(`\n[FOUND] ${id} has ${photoCount} photos in reviews.`);
            }

        } catch (e) {
            // 404 means no reviews usually or catalog issue
            // console.log(`Error ${id}: ${e.response?.status}`);
        }

        if (count % 10 === 0) process.stdout.write(`\rScanned ${count}/${allIds.length}`);
    }

    console.log(`\n\n--- RESULTS: Found ${found.length} Items with Photos ---`);
    found.forEach(f => console.log(`${f.id}: ${f.photoCount} photos`));
}

scanReviewsForPhotos();
