const axios = require('axios');
const fs = require('fs');

async function listOrders() {
    try {
        const token = fs.readFileSync('ml_token.txt', 'utf8').trim();
        const sellerId = '191028385'; // Extracted from previous interaction logs if available, or we fetch /users/me first

        // First get user ID to be sure
        const meRes = await axios.get('https://api.mercadolibre.com/users/me', {
            headers: { 'Authorization': `Bearer ${token}` }
        });
        const myId = meRes.data.id;
        console.log(`Logged in as User ID: ${myId}`);

        const response = await axios.get(`https://api.mercadolibre.com/orders/search?seller=${myId}&sort=date_desc&limit=4`, {
            headers: {
                'Authorization': `Bearer ${token}`
            }
        });

        console.log('--- Ultimas 4 Ventas en ML ---');
        response.data.results.forEach((o, index) => {
            const buyer = `${o.buyer.first_name} ${o.buyer.last_name}`;
            console.log(`#${index + 1} | ID: ${o.id} | Pack ID: ${o.pack_id || 'N/A'}`);
            console.log(`     📅 Fecha: ${o.date_created}`);
            console.log(`     👤 Comprador: ${buyer}`);
            console.log(`     💰 Total: $${o.total_amount}`);
            o.order_items.forEach(i => console.log(`     📦 Item: ${i.item.title} (x${i.quantity}) - $${i.unit_price}`));
            console.log('------------------------------------------------');
        });

    } catch (error) {
        console.error('Error fetching orders:', error.response ? error.response.data : error.message);
    }
}

listOrders();
