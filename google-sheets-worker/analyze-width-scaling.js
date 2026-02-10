require('dotenv').config();
const { GoogleSpreadsheet } = require('google-spreadsheet');
const { JWT } = require('google-auth-library');
const creds = require('./credentials.json');

async function analyzeScalingFinal() {
    const serviceAccountAuth = new JWT({
        email: creds.client_email,
        key: creds.private_key,
        scopes: ['https://www.googleapis.com/auth/spreadsheets'],
    });

    const doc = new GoogleSpreadsheet(process.env.SHEET_ID, serviceAccountAuth);
    await doc.loadInfo();
    const sheet = doc.sheetsByTitle['10x10mm'];

    await sheet.loadCells('A1:K300');

    // Hardcoded known 1m active price for stability
    const PRICE_1M_STANDARD = 20555;

    const sectionRanges = [];
    for (let r = 0; r < 300; r++) {
        [4, 5, 6].forEach(c => {
            const val = sheet.getCell(r, c).value;
            if (typeof val === 'string' && val.toLowerCase().includes('10x10mm')) {
                let name = val.replace(/10x10mm\s*/i, '').trim();
                if (!name.includes('0,9mm') && !name.includes('1m') && !name.includes('1mt')) { // Exclude 1m sections from auto-scan to avoid bad lookup
                    sectionRanges.push({ row: r, name: name });
                }
            }
        });
    }

    // Extract prices for smaller widths
    const items = [];

    // Add 1m manually
    items.push({ widthName: '1m', widthCm: 100, price: PRICE_1M_STANDARD });

    for (const sec of sectionRanges) {
        // Find 1m valid
        // Range: sec.row to sec.row+25 (approx)
        let bestPrice = Infinity;
        for (let r = sec.row; r < sec.row + 30; r++) {
            const m = sheet.getCell(r, 1).value;
            const price = sheet.getCell(r, 3).value;
            if (m === 1 && typeof price === 'number' && price > 2000) { // Safety floor
                if (price < bestPrice) bestPrice = price;
            }
        }

        let w = parseFloat(sec.name);
        if (bestPrice < Infinity) {
            items.push({ widthName: sec.name, widthCm: w, price: bestPrice });
        }
    }

    // Sort
    items.sort((a, b) => a.widthCm - b.widthCm);

    console.log("| Width | Actual Price | Actual $/cm | Suggested Price | Suggested $/cm | Check |");
    console.log("|---|---|---|---|---|---|");

    // Backward Pass
    // Anchor: 1m (Last Item)
    // Check P/cm from Largest to Smallest.
    // P/cm should ASCEND as Width DESCENDS.

    // We manipulate from Right to Left.
    // If Item[i] (Cheap) vs Item[i+1] (Expensive).
    // Item[i].PPCM must be > Item[i+1].PPCM.

    // Make a copy for calculations
    const calcItems = JSON.parse(JSON.stringify(items));

    for (let i = calcItems.length - 2; i >= 0; i--) {
        const curr = calcItems[i];
        const next = calcItems[i + 1];

        curr.ppcm = curr.price / curr.widthCm;
        next.ppcm = next.price / next.widthCm;

        if (curr.ppcm <= next.ppcm) {
            // Violation. Smaller has lower/equal unit cost. 
            // Should be higher.
            // Set Target PPCM = Next.PPCM * 1.05 (5% markup for smaller size)
            const newPPCM = next.ppcm * 1.05;
            curr.newPrice = Math.ceil(newPPCM * curr.widthCm);
            curr.ppcm = newPPCM; // Update for further propagation leftwards
            curr.note = "⚠️ Adjusted Up";
        } else {
            curr.newPrice = curr.price;
            curr.note = "OK";
        }
    }

    // 1m is anchor
    calcItems[calcItems.length - 1].newPrice = calcItems[calcItems.length - 1].price;
    calcItems[calcItems.length - 1].note = "Anchor";

    calcItems.forEach(i => {
        const actPPCM = (i.price / i.widthCm).toFixed(0);
        const sugPPCM = (i.newPrice / i.widthCm).toFixed(0);
        console.log(`| ${i.widthName} | $${i.price.toLocaleString('es-AR')} | $${actPPCM} | $${i.newPrice.toLocaleString('es-AR')} | $${sugPPCM} | ${i.note} |`);
    });
}

analyzeScalingFinal();
