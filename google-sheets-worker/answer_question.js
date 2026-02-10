const fs = require('fs');
const axios = require('axios');

const TARGET_USER_ID = 333350;
const ANSWER_TEXT = "¡Hola! este es el modelo de rombo mas chico que tenemos.";

async function answerQuestion() {
    try {
        const token = fs.readFileSync('ml_token.txt', 'utf8').trim();

        // 1. Get Seller ID
        const meRes = await axios.get('https://api.mercadolibre.com/users/me', {
            headers: { Authorization: `Bearer ${token}` }
        });
        const sellerId = meRes.data.id;

        // 2. Find Pending Questions (Fetch all unanswered first)
        console.log(`Searching for pending questions...`);
        const qRes = await axios.get('https://api.mercadolibre.com/questions/search', {
            headers: { Authorization: `Bearer ${token}` },
            params: {
                seller_id: sellerId,
                status: 'UNANSWERED',
                limit: 50 // Fetch enough to find our target
            }
        });

        const allQuestions = qRes.data.questions;

        // Filter locally for the specific user
        const questions = allQuestions.filter(q => q.from.id == TARGET_USER_ID);

        if (questions.length === 0) {
            console.log('❌ Question not found (maybe executed already?).');
            return;
        }

        // Assuming the first one is the matching one (rare to have multiple unanswered from same user on same item at once, but we take the first)
        const q = questions[0];
        console.log(`Found Question ID: ${q.id}`);
        console.log(`Text: "${q.text}"`);

        // 3. Post Answer
        console.log(`Sending Answer: "${ANSWER_TEXT}"...`);
        const ansRes = await axios.post('https://api.mercadolibre.com/answers', {
            question_id: q.id,
            text: ANSWER_TEXT
        }, {
            headers: { Authorization: `Bearer ${token}` }
        });

        console.log('✅ Answer Sent Successfully!');
        console.log('Answer ID:', ansRes.data.id);

    } catch (e) {
        console.error('Error:', e.message);
        if (e.response) console.error('API Response:', JSON.stringify(e.response.data, null, 2));
    }
}

answerQuestion();
