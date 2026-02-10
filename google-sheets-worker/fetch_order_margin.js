const axios = require('axios');
const fs = require('fs');

async function getOrderDetails() {
    try {
        const token = fs.readFileSync('ml_token.txt', 'utf8').trim();
        const orderId = '2000015046917810';

        const response = await axios.get(`https://api.mercadolibre.com/orders/${orderId}`, {
            headers: {
                'Authorization': `Bearer ${token}`
            }
        });

        const order = response.data;
        console.log('--- Order Details ---');
        console.log(`ID: ${order.id}`);
        console.log(`Taxes Object:`, JSON.stringify(order.taxes, null, 2));
        console.log(`Pack ID: ${order.pack_id}`);
        console.log(`Date: ${order.date_created}`);
        console.log(`Total Amount: ${order.total_amount}`);
        console.log(`Paid Amount: ${order.paid_amount}`);

        console.log('\n--- Items ---');
        order.order_items.forEach(item => {
            console.log(`- Title: ${item.item.title}`);
            console.log(`  Quantity: ${item.quantity}`);
            console.log(`  Unit Price: ${item.unit_price}`);
            console.log(`  Sale Fee: ${item.sale_fee}`);
            console.log(`  Listing Type: ${item.listing_type_id}`);
        });

        console.log('\n--- Payments ---');
        order.payments.forEach(p => {
            console.log(`Payment ID: ${p.id}`);
            console.log(`  Transaction Amount: ${p.transaction_amount}`);
            console.log(`  Total Paid: ${p.total_paid_amount}`);
            console.log(`  Marketplace Fee: ${p.marketplace_fee}`);
            console.log(`  Shipping Cost (Seller): ${p.shipping_cost}`);
            console.log(`  Net Received: ${p.transaction_amount - (p.marketplace_fee || 0) - (p.shipping_cost || 0)} (Estimate)`);
            console.log(`  Status Details: ${p.status_detail}`);
        });

        console.log('\n--- Taxes (if any) ---');
        if (order.taxes && order.taxes.amount) {
            console.log(`Order Taxes: ${order.taxes.amount}`);
        } else {
            console.log('No order-level taxes found.');
        }

        console.log('\n--- Shipping ---');
        if (order.shipping && order.shipping.id) {
            console.log(`Shipping ID: ${order.shipping.id}`);
            // Try to fetch shipment to see cost
            try {
                const shipRes = await axios.get(`https://api.mercadolibre.com/shipments/${order.shipping.id}`, { headers: { 'Authorization': `Bearer ${token}` } });
                const ship = shipRes.data;
                console.log(`  Mode: ${ship.mode}`);
                console.log(`  Logistic Type: ${ship.logistic_type}`);
                console.log(`  Free Shipping: ${ship.free_shipping}`);
                console.log(`  Cost to Sender: ${ship.shipping_option ? ship.shipping_option.list_cost : 'N/A'}`);
            } catch (e) { console.log("  Could not fetch detailed shipment info."); }
        }

    } catch (error) {
        console.error('Error fetching order:');
        if (error.response) {
            console.error(error.response.data);
        } else {
            console.error(error.message);
        }
    }
}

getOrderDetails();
