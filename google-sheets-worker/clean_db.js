const fs = require('fs');
console.log("Cleaning DB...");
try {
    const products = require('./products_db.json');
    console.log(`Original size: ${products.length}`);

    const unique = [];
    const seen = new Set();

    products.forEach(p => {
        // Strip hash
        let cleanUrl = p.url.split('#')[0];
        // Ensure HTTPS
        if (cleanUrl.startsWith('http:')) cleanUrl = cleanUrl.replace('http:', 'https:');

        if (!seen.has(cleanUrl)) {
            seen.add(cleanUrl);
            // Update URL
            p.url = cleanUrl;
            unique.push(p);
        }
    });

    fs.writeFileSync('products_db.json', JSON.stringify(unique, null, 2));
    console.log(`✅ Cleaned DB. New size: ${unique.length}`);
} catch (e) {
    console.error(e);
}
