const axios = require('axios');

async function checkPage() {
    try {
        const url = 'https://www.ferrindep.com.ar/productos/producto/1';
        console.log(`Fetching ${url}...`);
        const res = await axios.get(url);
        console.log("HTML Preview (first 2000 chars):");
        console.log(res.data.substring(0, 2000));

        // Check for specific keywords related to 40cm or heights
        console.log("\n--- SEARCHING FOR '40' or 'cm' ---");
        const lines = res.data.split('\n');
        lines.forEach((line, i) => {
            if (line.includes('40') || line.includes('cm') || line.includes('metro')) {
                console.log(`Line ${i}: ${line.trim()}`);
            }
        });
    } catch (e) {
        console.error(e.message);
    }
}

checkPage();
