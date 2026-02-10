const fs = require('fs');
const axios = require('axios');
const path = require('path');
require('dotenv').config();

// Load Credentials
const CLIENT_ID = process.env.ML_CLIENT_ID || '8151209354501306';
const CLIENT_SECRET = process.env.ML_CLIENT_SECRET;

async function forceRefresh() {
    try {
        console.log('Forcing Token Refresh...');

        let refreshToken = '';
        if (fs.existsSync('ml_refresh_token.txt')) {
            refreshToken = fs.readFileSync('ml_refresh_token.txt', 'utf8').trim();
        } else {
            console.error('❌ No refresh token file found!');
            return;
        }

        console.log(`Using Refresh Token: ${refreshToken.substring(0, 10)}...`);

        if (!CLIENT_SECRET) {
            console.error('❌ Missing ML_CLIENT_SECRET in .env');
            return;
        }

        const res = await axios.post('https://api.mercadolibre.com/oauth/token', {
            grant_type: 'refresh_token',
            client_id: CLIENT_ID,
            client_secret: CLIENT_SECRET,
            refresh_token: refreshToken
        }, {
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
        });

        const data = res.data;
        console.log('✅ Token Refreshed Successfully!');

        // Save New Tokens
        fs.writeFileSync('ml_token.txt', data.access_token);
        console.log('Saved ml_token.txt');

        if (data.refresh_token) {
            fs.writeFileSync('ml_refresh_token.txt', data.refresh_token);
            console.log('Saved ml_refresh_token.txt');
        }

    } catch (e) {
        console.error('Error refreshing token:', e.message);
        if (e.response) {
            console.error('API Error:', JSON.stringify(e.response.data, null, 2));
        }
    }
}

forceRefresh();
