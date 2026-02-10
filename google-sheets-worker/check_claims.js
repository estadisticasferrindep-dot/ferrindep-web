const fs = require('fs');
const axios = require('axios');

async function checkClaims() {
    try {
        const token = fs.readFileSync('ml_token.txt', 'utf8').trim();
        const meRes = await axios.get('https://api.mercadolibre.com/users/me', {
            headers: { Authorization: `Bearer ${token}` }
        });
        const sellerId = meRes.data.id;
        console.log(`Checking claims for Seller ID: ${sellerId}...`);

        // Using v1 search endpoint for claims
        const res = await axios.get('https://api.mercadolibre.com/post-purchase/v1/claims/search', {
            headers: { Authorization: `Bearer ${token}` },
            params: {
                seller_id: sellerId,
                status: 'opened',
                sort: 'last_updated:desc'
            }
        });

        // The API returns { paging: {...}, data: [...] }
        const claims = res.data.data || [];

        if (claims.length === 0) {
            console.log('✅ No open claims found.');
            return;
        }

        console.log(`⚠️ Found ${claims.length} OPEN claims!`);
        claims.forEach(c => {
            console.log(`- ID: ${c.id} | Type: ${c.type} | Stage: ${c.stage} | Status: ${c.status}`);
            console.log(`  Resource: ${c.resource_id} (Order/Payment)`);
        });

    } catch (e) {
        console.error('Error checking claims:', e.message);
        if (e.response) console.log(JSON.stringify(e.response.data, null, 2));
    }
}

checkClaims();
