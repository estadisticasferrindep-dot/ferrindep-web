require('dotenv').config();
const { GoogleSpreadsheet } = require('google-spreadsheet');
const { JWT } = require('google-auth-library');
const creds = require('./credentials.json');

// --- CONFIGURATION ---
const SHEET_TITLE = '15x15mm';
const TARGET_FLOOR = 0.2500;
const TARGET_AIM = 0.2510; // Aim slightly higher (25.10%) to be safe
const COL_N = 13; // Variable
const COL_T = 19; // Margin
const COL_SKU = 0; // SKU (Col A) 
const COL_DESC = 5; // Description (Col F)

async function auditAndFix() {
    const auth = new JWT({
        email: creds.client_email,
        key: creds.private_key,
        scopes: ['https://www.googleapis.com/auth/spreadsheets'],
    });

    const doc = new GoogleSpreadsheet(process.env.SHEET_ID, auth);
    await doc.loadInfo();
    const sheet = doc.sheetsByTitle[SHEET_TITLE];

    console.log(`Loaded Sheet: ${doc.title} > ${sheet.title}`);

    // Load range. Assuming data is within first 200 rows for now? 
    // Or check rowCount. 15x15mm has 1000+ rows but maybe empty?
    // Let's load first 500 rows.
    await sheet.loadCells('A1:T500');

    const fixedItems = [];

    // Scan rows starting from Index 4 (Row 5)
    for (let r = 4; r < 500; r++) {
        const cellDesc = sheet.getCell(r, COL_DESC);
        if (!cellDesc.value) continue; // Skip empty rows

        const cellT = sheet.getCell(r, COL_T);
        // Check if numerical
        if (typeof cellT.value !== 'number') continue;

        const currentMargin = cellT.value;

        // Tolerance: If it's 24.99% maybe ignore? User said "menos de 25%".
        // Let's set threshold strictly < 0.25.
        if (currentMargin < TARGET_FLOOR - 0.0001) { // < 24.99%
            const sku = sheet.getCell(r, COL_SKU).value;
            const desc = cellDesc.value;

            console.log(`[FOUND LOW MARGIN] R${r + 1} | SKU:${sku} | Desc:${desc} | Margin: ${(currentMargin * 100).toFixed(2)}%`);

            // Fix it
            const finalMargin = await solveRow(sheet, r);

            fixedItems.push({
                row: r + 1,
                sku: sku,
                desc: desc,
                oldMargin: (currentMargin * 100).toFixed(2) + '%',
                newMargin: (finalMargin * 100).toFixed(2) + '%'
            });
        }
    }

    // Report
    console.log("\n--- SUMMARY OF FIXES ---");
    if (fixedItems.length === 0) {
        console.log("No items found below 25%.");
    } else {
        console.table(fixedItems);
        // Print detailed list so we can copy-paste to user
        console.log("\nListado de SKUs Arreglados:");
        fixedItems.forEach(i => console.log(`- Fila ${i.row}: ${i.sku || 'S/N'} (${i.desc}) -> Estaba en ${i.oldMargin}, Ahora ${i.newMargin}`));
    }
}

async function solveRow(sheet, rowIndex) {
    const MAX_ITER = 20;
    let prevN = null; let prevT = null;

    // We already loaded cells for A1:T500, but solveRow might modify and save/reload.
    // To be safe and see updates, we should reload the ROW specifically inside the loop?
    // Or rely on the cache if we update `cell.value` correctly?
    // `saveUpdatedCells` updates the sheet. We need to reload to get RECALCULATED T.

    for (let i = 0; i < MAX_ITER; i++) {
        // Must reload to verify formula results from Sheet Engine
        await sheet.loadCells({
            startRowIndex: rowIndex, endRowIndex: rowIndex + 1,
            startColumnIndex: 0, endColumnIndex: 20
        });

        const cellN = sheet.getCell(rowIndex, COL_N);
        const cellT = sheet.getCell(rowIndex, COL_T);

        let currentN = typeof cellN.value === 'number' ? cellN.value : 0.5;
        let currentT = typeof cellT.value === 'number' ? cellT.value : 0;

        if (currentT >= TARGET_FLOOR && currentT <= (TARGET_FLOOR + 0.005)) {
            // Good enough
            return currentT;
        }

        // If we overshoot (> 25.5%), we might want to bring it down closer to 25.10% if desired?
        // User said "normalices... modifcando N". Usually implies finding the floor.
        // But if it's already > 25, we shouldn't touch it (filtered by main loop).
        // Inside solver, if we overshoot to 30%, we should correct down.

        let newN;
        if (prevN === null) {
            // Initial Step
            newN = currentN + (TARGET_AIM - currentT) * 0.5;
        } else {
            const slope = (currentT - prevT) / (currentN - prevN);

            if (Math.abs(slope) < 1e-9) {
                newN = currentN + 0.05;
            } else {
                const rawStep = (TARGET_AIM - currentT) / slope;
                const maxStep = 0.3;
                const clampedStep = Math.max(Math.min(rawStep, maxStep), -maxStep);
                newN = currentN + clampedStep;
            }
        }

        // Noise
        if (newN === currentN || newN === prevN) {
            newN += 0.001 * (Math.random() > 0.5 ? 1 : -1);
        }

        if (newN < -0.9) newN = -0.5;

        prevN = currentN; prevT = currentT;

        cellN.value = Number(newN.toFixed(4));
        await sheet.saveUpdatedCells();
        await new Promise(r => setTimeout(r, 1200));
    }

    // Return final observed
    return sheet.getCell(rowIndex, COL_T).value;
}

auditAndFix();
