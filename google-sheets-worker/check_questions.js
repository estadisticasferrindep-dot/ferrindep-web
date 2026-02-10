const fs = require('fs');
const axios = require('axios');

async function checkQuestions() {
    try {
        const token = fs.readFileSync('ml_token.txt', 'utf8').trim();

        // 1. Get User ID (Seller ID)
        const meRes = await axios.get('https://api.mercadolibre.com/users/me', {
            headers: { Authorization: `Bearer ${token}` }
        });
        const sellerId = meRes.data.id;
        console.log(`Checking questions for Seller ID: ${sellerId}...`);

        // 2. Search Unanswered Questions
        const qRes = await axios.get('https://api.mercadolibre.com/questions/search', {
            headers: { Authorization: `Bearer ${token}` },
            params: {
                seller_id: sellerId,
                status: 'UNANSWERED',
                sort_fields: 'date_created',
                sort_types: 'ASC'
            }
        });

        const questions = qRes.data.questions;

        if (questions.length === 0) {
            console.log('✅ No pending questions! All caught up.');
            return;
        }

        console.log(`\nfound ${questions.length} pending questions:\n`);

        for (const q of questions) {
            // Fetch item title for context
            let itemTitle = q.item_id;
            try {
                const iRes = await axios.get(`https://api.mercadolibre.com/items/${q.item_id}`, {
                    headers: { Authorization: `Bearer ${token}` }
                });
                itemTitle = iRes.data.title;
            } catch (e) { /* ignore item fetch error */ }

            console.log(`🔸 [${new Date(q.date_created).toLocaleString()}]`);
            console.log(`   Item: ${itemTitle} (${q.item_id})`);
            console.log(`   User: ${q.from.id}`);
            console.log(`   🗣️ "${q.text}"`);
            console.log('   -------------------------------------------------');
        }

    } catch (e) {
        console.error('Error:', e.message);
        if (e.response) console.error('API Response:', JSON.stringify(e.response.data, null, 2));
    }
}

checkQuestions();
