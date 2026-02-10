const axios = require('axios');
const fs = require('fs');

async function getSkus() {
    const ids = ['MLA801222637', 'MLA2305330562', 'MLA2472352522'];
    const token = fs.readFileSync('ml_token.txt', 'utf8').trim();

    try {
        const res = await axios.get(`https://api.mercadolibre.com/items?ids=${ids.join(',')}`, {
            headers: { Authorization: `Bearer ${token}` }
        });

        res.data.forEach(item => {
            if (item.code === 200) {
                const skuAttr = item.body.attributes.find(a => a.id === 'SELLER_SKU');
                const sku = skuAttr ? skuAttr.value_name : 'N/A';
                console.log(`${item.body.id}: SKU ${sku} - ${item.body.title}`);
            }
        });
    } catch (e) { console.error(e.message); }
}

getSkus();
