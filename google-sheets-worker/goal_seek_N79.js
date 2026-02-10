require('dotenv').config();
const { GoogleSpreadsheet } = require('google-spreadsheet');
const { JWT } = require('google-auth-library');
const creds = require('./credentials.json');

const TARGET_MARGIN = 0.25;
const MAX_ITERATIONS = 15;
const ROW_IDX = 78; // Row 79 (0-indexed)
const COL_N = 13;   // Column N
const COL_T = 19;   // Column T

async function goalSeekStateful() {
    const serviceAccountAuth = new JWT({
        email: creds.client_email,
        key: creds.private_key,
        scopes: ['https://www.googleapis.com/auth/spreadsheets'],
    });

    // Support both ID formats seen in .env
    const sheetID = process.env.SHEET_ID_PRICING || process.env.SHEET_ID;

    try {
        const doc = new GoogleSpreadsheet(sheetID, serviceAccountAuth);
        await doc.loadInfo();
        console.log(`Loaded Doc: ${doc.title}`);

        const sheet = doc.sheetsByTitle['50x50mm'];
        if (!sheet) {
            console.error("Tab '50x50mm' not found. Available: " + doc.sheetsByIndex.map(s => s.title).join(', '));
            return;
        }

        let prevN = null;
        let prevT = null;

        console.log(`Starting Goal Seek on '50x50mm' Row 79. Target T=${(TARGET_MARGIN * 100)}%`);

        for (let i = 0; i < MAX_ITERATIONS; i++) {
            // Load specific range for Row 79 (Indices: Row 78, Cols N(13) to T(19))
            // Loading strictly N79:T79
            await sheet.loadCells('N79:T79');

            const cellN = sheet.getCell(ROW_IDX, COL_N);
            const cellT = sheet.getCell(ROW_IDX, COL_T);

            const ValN = cellN.value; // Percentage e.g. 0.55
            const ValT = cellT.value; // Margin e.g. 0.15

            console.log(`#${i + 1}: Current N=${(ValN * 100).toFixed(2)}%, Margin T=${(ValT * 100).toFixed(2)}%`);

            if (Math.abs(ValT - TARGET_MARGIN) < 0.002) { // 0.2% tolerance
                console.log("✅ Converged! Target reached.");
                break;
            }

            let newN;

            if (prevN === null) {
                // Initial heuristics
                // If T=0.15 (Low), and we want 0.25.
                // Assuming positive correlation (Higher N = Higher Price = Higher Margin).
                // Try +5% bump.
                newN = ValN + 0.05;
            } else {
                // Secant Method
                const dN = ValN - prevN;
                const dT = ValT - prevT;

                if (Math.abs(dT) < 0.00001) {
                    console.log("⚠️ Gradient zero (Flat). Nudging...");
                    newN = ValN + 0.05;
                } else {
                    const slope = dT / dN;
                    const gap = TARGET_MARGIN - ValT;
                    newN = ValN + (gap / slope);

                    // Damping to prevent wild oscillations if slope is tiny
                    // If prediction is huge, clamp it.
                    if (Math.abs(newN - ValN) > 0.5) {
                        console.log("⚠️ Large jump predicted, clamping to +/- 10%");
                        const sign = Math.sign(newN - ValN);
                        newN = ValN + (sign * 0.1);
                    }
                }
            }

            // Safety clamps (0% to 500%)
            if (newN < 0) newN = 0.01;
            if (newN > 5) newN = 5.0;

            console.log(`   -> Adjusting N to ${(newN * 100).toFixed(2)}%`);

            prevN = ValN;
            prevT = ValT;

            cellN.value = newN;
            await sheet.saveUpdatedCells();

            // Wait a small bit for Sheets to propogate? Usually not needed with loadCells, but good practice.
            await new Promise(r => setTimeout(r, 1000));
        }
    } catch (error) {
        console.error("CRITICAL ERROR:", error);
    }
}

goalSeekStateful();
