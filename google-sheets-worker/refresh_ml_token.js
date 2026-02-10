const axios = require('axios');
const fs = require('fs');
const qs = require('qs');

const CLIENT_ID = '8151209354501306';
const CLIENT_SECRET = 'O9RW50Qs0vXzKPosLE4Hz9EX2S0SKWbE';
const REFRESH_TOKEN_FILE = 'ml_refresh_token.txt';

async function refresh() {
    try {
        if (!fs.existsSync(REFRESH_TOKEN_FILE)) {
            throw new Error(`Refresh Token file '${REFRESH_TOKEN_FILE}' not found.`);
        }
        const refreshToken = fs.readFileSync(REFRESH_TOKEN_FILE, 'utf8').trim();

        console.log('Refreshing token using:', refreshToken);

        const response = await axios.post('https://api.mercadolibre.com/oauth/token',
            qs.stringify({
                grant_type: 'refresh_token',
                client_id: CLIENT_ID,
                client_secret: CLIENT_SECRET,
                refresh_token: refreshToken
            }),
            {
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
            }
        );

        const data = response.data;
        console.log('✅ SUCCESS! Token Refreshed.');
        console.log('New Access Token:', data.access_token.substring(0, 15) + '...');
        console.log('New Refresh Token:', data.refresh_token);

        fs.writeFileSync('ml_token.txt', data.access_token);
        fs.writeFileSync('ml_refresh_token.txt', data.refresh_token);

        console.log('Tokens updated in files.');

    } catch (error) {
        console.error('❌ Error refreshing token:', error.response ? error.response.data : error.message);
    }
}

refresh();
