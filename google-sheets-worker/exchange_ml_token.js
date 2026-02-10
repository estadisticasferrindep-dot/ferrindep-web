const axios = require('axios');
const fs = require('fs');
const qs = require('qs'); // Axios usually handles object -> form-urlencoded but let's be safe if needed, though simple object works often with axios. Actually axios auto-serializes to JSON by default. For application/x-www-form-urlencoded we need qs or URLSearchParams. 

const CLIENT_ID = '8151209354501306';
const CLIENT_SECRET = 'O9RW50Qs0vXzKPosLE4Hz9EX2S0SKWbE';
const CODE = 'TG-697ff078025cc100016fa0af-97128565';
const REDIRECT_URI = 'https://www.google.com';

async function exchange() {
    try {
        console.log('Exchanging code...');
        const response = await axios.post('https://api.mercadolibre.com/oauth/token',
            qs.stringify({
                grant_type: 'authorization_code',
                client_id: CLIENT_ID,
                client_secret: CLIENT_SECRET,
                code: CODE,
                redirect_uri: REDIRECT_URI
            }),
            {
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
            }
        );

        const data = response.data;
        console.log('SUCCESS!');
        console.log('Access Token (truncated):', data.access_token.substring(0, 15) + '...');
        console.log('Refresh Token:', data.refresh_token);

        fs.writeFileSync('ml_token.txt', data.access_token);
        fs.writeFileSync('ml_refresh_token.txt', data.refresh_token);

        console.log('Tokens saved to files.');

    } catch (error) {
        console.error('Error exchanging token:', error.response ? error.response.data : error.message);
    }
}

exchange();
