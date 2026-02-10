require('dotenv').config();
const { GoogleSpreadsheet } = require('google-spreadsheet');
const { JWT } = require('google-auth-library');
const creds = require('./credentials.json');

// Configuration
const TARGET_TAB = '15x15mm';
const START_ROW = 224;
const END_ROW = 242; // Inclusive
const IGNORED_ROWS = [226, 228, 229, 231, 232, 234, 235, 236, 238, 239, 240, 242]; // User Exclusions + Empty Rows
const TARGET_MARGIN = 0.25;

const COL_N = 13; // Index
const COL_T = 19; // Index
const COL_E = 4;  // Index (Price per meter)

async function escalatePrices() {
    const serviceAccountAuth = new JWT({
        email: creds.client_email,
        key: creds.private_key,
        scopes: ['https://www.googleapis.com/auth/spreadsheets'],
    });

    const sheetID = process.env.SHEET_ID_PRICING || process.env.SHEET_ID;
    const doc = new GoogleSpreadsheet(sheetID, serviceAccountAuth);

    try {
        await doc.loadInfo();
        const sheet = doc.sheetsByTitle[TARGET_TAB];
        if (!sheet) { console.error(`Tab '${TARGET_TAB}' not found`); return; }

        console.log(`--- Scaling Prices: Row ${START_ROW} to ${END_ROW} ---`);

        // We process BACKWARDS (from Longest Roll to Shortest Roll)
        // Because the Longest Roll is the "Anchor" (Cheapest per meter).
        // Shorter rolls must be Equal or More Expensive per meter.

        let floorPricePerMeter = 0;

        for (let r = END_ROW; r >= START_ROW; r--) {
            // Check Explicit Ignore List
            if (IGNORED_ROWS.includes(r)) {
                console.log(`Skipping Row ${r} (Ignored List)`);
                continue;
            }

            const rowIdx = r - 1; // 0-indexed

            // Load Row
            await sheet.loadCells({
                startRowIndex: rowIdx, endRowIndex: rowIdx + 1,
                startColumnIndex: 0, endColumnIndex: 20
            });

            const cellN = sheet.getCell(rowIdx, COL_N);
            const cellT = sheet.getCell(rowIdx, COL_T);
            const cellE = sheet.getCell(rowIdx, COL_E);

            const valT = cellT.value;
            // Check Empty (Unpublished)
            if (valT === null || valT === '' || valT === undefined) {
                console.log(`Skipping Row ${r} (Empty/Unpublished)`);
                continue;
            }

            const currentE = cellE.value;
            const currentN = cellN.value;

            console.log(`Row ${r}: E=$${currentE?.toFixed(2)}/m | T=${(valT * 100).toFixed(2)}% | Floor Required: $${floorPricePerMeter.toFixed(2)}`);

            let targetE = currentE;
            let needsAdjustment = false;
            let reason = "";

            // Condition 1: Must satisfy Floor (Monotonicity)
            if (floorPricePerMeter > 0 && currentE < floorPricePerMeter) {
                // Violation! 
                // E.g. Current=100, Floor=110.
                // We must raise Current to 110.
                targetE = floorPricePerMeter;
                needsAdjustment = true;
                reason = "Scaling Curve";
            }

            // Condition 2: Must satisfy Margin (Safety)
            // If T < 25%, we MUST raise N regardless of E.
            // This loop handles E-targeting. If T is low, E will arguably range up. 
            // But we can't easily Target E based on T-failure inside 'solveForE'.
            // Actually, if we just solved N for 25% previously, T should be >= 25%.
            // But let's double check.
            if (valT < (TARGET_MARGIN - 0.001)) {
                console.log(`   ⚠️ Warning: Row ${r} Margin ${(valT * 100).toFixed(2)}% < 25%. Forcing Margin 25% logic first.`);
                // Force solve for Margin 25% first
                await solveForMargin(sheet, rowIdx, TARGET_MARGIN);
                // Reload values after solve
                targetE = cellE.value; // It changed
                console.log(`   -> New Base E after Margin fix: $${targetE.toFixed(2)}`);
                // Re-evaluate floor
                if (targetE < floorPricePerMeter) {
                    targetE = floorPricePerMeter;
                    needsAdjustment = true;
                    reason = "Scaling Curve (Post-Margin Fix)";
                } else {
                    needsAdjustment = false; // Margin fix was enough to clear floor?
                }
            }

            if (needsAdjustment) {
                console.log(`   🔸 Adjusting Row ${r} due to ${reason}. Target E >= ${targetE.toFixed(2)}`);
                await solveForTargetE(sheet, rowIdx, targetE);
                // Update local var for floor propogation
                floorPricePerMeter = cellE.value;
            } else {
                // If current E is valid, it becomes the new floor for the next (shorter) item.
                // i.e. The 6m roll must be >= the 7m roll's price.
                floorPricePerMeter = Math.max(floorPricePerMeter, currentE);
            }

            console.log(`   ✅ Final E: $${cellE.value.toFixed(2)} (Sets floor for Row ${r - 1})`);
        }

        console.log("-----------------------------------------");
        console.log("Scaling Verification Complete.");

    } catch (e) {
        console.error("Critical Error", e);
    }
}

// Solves N for Target Margin (T)
async function solveForMargin(sheet, rIdx, targetVal) {
    const cellN = sheet.getCell(rIdx, COL_N);
    const cellT = sheet.getCell(rIdx, COL_T);

    // Simple heuristic loop
    for (let i = 0; i < 10; i++) {
        const currT = cellT.value;
        if (currT >= targetVal) break; // Good enough (one-sided)

        const currN = cellN.value;
        // Simple bump
        const delta = targetVal - currT;
        // Assuming slope ~0.8?
        const newN = currN + (delta * 1.5);
        cellN.value = newN;

        await sheet.saveUpdatedCells();
        await new Promise(r => setTimeout(r, 600)); // Wait recalc

        // Reload to check
        await sheet.loadCells({
            startRowIndex: rIdx, endRowIndex: rIdx + 1,
            startColumnIndex: COL_N, endColumnIndex: COL_T + 1
        });
    }
}

// Solves N for Target Price Per Meter (E)
async function solveForTargetE(sheet, rIdx, targetE) {
    const cellN = sheet.getCell(rIdx, COL_N);
    const cellE = sheet.getCell(rIdx, COL_E);

    let prevN = null;
    let prevE = null;

    for (let i = 0; i < 10; i++) {
        const currE = cellE.value;
        const currN = cellN.value;

        // Tolerance: $1? 
        if (!currE || typeof currE !== 'number') {
            console.error("      Error: Current E is invalid/empty inside solver.");
            break;
        }

        if (currE >= targetE && (currE - targetE) < 10) {
            // We are above target (valid) and close enough.
            break;
        }

        // If we are significantly below, we need to raise N.
        let newN;

        if (prevN === null) {
            // Heuristic: E is roughly Price/Length. Price ~ Cost * (1+N).
            // So E is linear-ish with N.
            // If E=1000, Target=1100 (+10%).
            // We likely need to raise N by some factor.
            // Let's rely on Secant quickly.
            // Initial nudge:
            const ratio = targetE / currE;
            // If ratio is 1.1, try increasing N by 10% relative? 
            // or Just Add 0.05.
            newN = currN * ratio;
        } else {
            // Secant
            const dN = currN - prevN;
            const dE = currE - prevE;

            if (Math.abs(dE) < 0.01) {
                newN = currN + 0.05;
            } else {
                const slope = dE / dN;
                const gap = targetE - currE;
                newN = currN + (gap / slope);
            }
        }

        // Clamp
        if (newN < 0) newN = 0.01;
        if (newN > 10) newN = 10;

        console.log(`      Iter ${i}: E=$${currE.toFixed(2)} -> Goal $${targetE.toFixed(2)}. Adjusting N to ${(newN * 100).toFixed(2)}%`);

        prevN = currN;
        prevE = currE;

        cellN.value = newN;
        await sheet.saveUpdatedCells();
        await new Promise(r => setTimeout(r, 800)); // Wait recalc

        // Reload E
        await sheet.loadCells({
            startRowIndex: rIdx, endRowIndex: rIdx + 1,
            startColumnIndex: 0, endColumnIndex: 20
        });
    }
}

escalatePrices();
