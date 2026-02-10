const { GoogleSpreadsheet } = require('google-spreadsheet');
const { JWT } = require('google-auth-library');
require('dotenv').config();
const creds = require('./credentials.json');

async function simulate() {
    const serviceAccountAuth = new JWT({
        email: creds.client_email,
        key: creds.private_key,
        scopes: ['https://www.googleapis.com/auth/spreadsheets'],
    });

    const doc = new GoogleSpreadsheet(process.env.SHEET_ID, serviceAccountAuth);
    await doc.loadInfo();
    const sheet = doc.sheetsByTitle['20x20mm'];

    // Row 67 (Index 66)
    const ROW_IDX = 66;

    await sheet.loadCells(`A${ROW_IDX + 1}:T${ROW_IDX + 1}`);

    // Helpers
    const getVal = (idx) => {
        const cell = sheet.getCell(ROW_IDX, idx);
        let val = cell.formattedValue || cell.value;
        if (typeof val === 'string') {
            val = parseFloat(val.replace(/\$/g, '').replace(/\./g, '').replace(/,/g, '.').trim());
        }
        return typeof val === 'number' ? val : 0;
    };

    const sku = sheet.getCell(ROW_IDX, 0).value;
    const cost = getVal(2); // Col C
    const price = getVal(3); // Col D (Assuming Title is B, Cost C, Price D? Verify column map)
    // Wait, in Step 229 dump:
    // Col A: SKU
    // Col C: Cost (68989)
    // Col D: Price (143461)
    // Col F: Fee (-19367)
    // Col I: Shipping (-11110)
    // Col L: Shipping (Cost?) No.
    // Let's use the dump structure from Step 229.

    const currentPrice = getVal(3); // D
    const currentFee = getVal(5); // F (Negative)
    const currentShipping = getVal(8); // I (Negative)
    const currentTaxes = getVal(6); // G (Impuestos? Step 229 had -2151 in G)
    // Please verify Step 229: G was "Col G: -2151,92". Yes.

    console.log(`--- SKU ${sku} Current Data ---`);
    console.log(`Price: $${currentPrice}`);
    console.log(`Fee: $${currentFee}`);
    console.log(`Shipping: $${currentShipping}`);
    console.log(`Taxes: $${currentTaxes}`);
    console.log(`Cost: $${cost}`);

    // Calculation Logic
    // Fee Rate
    const feeRate = Math.abs(currentFee / currentPrice);
    console.log(`Implied Fee Rate: ${(feeRate * 100).toFixed(2)}%`);

    // New Scanario
    const discount = 0.05;
    const newPrice = Math.floor(currentPrice * (1 - discount));
    const newFee = -(newPrice * feeRate);
    // Shipping remains same (unless free shipping threshold... usually >$30k is free, assume same)
    // Taxes... fix or %, usually % of net? IIBB varies. Assume fixed rate on price? Or fixed % of gross?
    // IIBB is usually % of Gross (Taxable Amount).
    const taxRate = Math.abs(currentTaxes / currentPrice);
    const newTaxes = -(newPrice * taxRate); // Estimate

    // Advance Cost Logic (4.6% on Net)
    // Net = NewPrice + NewFee + Shipping + Taxes
    // (Note: fee, shipping, taxes are negative)
    const newNetIncome = newPrice + newFee + currentShipping + newTaxes;
    const newAdvance = -(newNetIncome * 0.046);

    const newNetHand = newNetIncome + newAdvance; // Advance is neg
    const newProfit = newNetHand - cost;
    const newMargin = (newProfit / newPrice) * 100;

    console.log(`\n--- Simulation (-5%) ---`);
    console.log(`New Price: $${newPrice}`);
    console.log(`New Fee: $${newFee.toFixed(2)}`);
    console.log(`New Taxes (Est): $${newTaxes.toFixed(2)}`);
    console.log(`Shipping: $${currentShipping}`);
    console.log(`New Net (Pre-Adv): $${newNetIncome.toFixed(2)}`);
    console.log(`New Advance (4.6%): $${newAdvance.toFixed(2)}`);
    console.log(`New Net Hand: $${newNetHand.toFixed(2)}`);
    console.log(`Profit: $${newProfit.toFixed(2)}`);
    console.log(`New Margin: ${newMargin.toFixed(2)}%`);
}

simulate();
