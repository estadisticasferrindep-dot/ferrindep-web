require('dotenv').config();
const { GoogleSpreadsheet } = require('google-spreadsheet');
const { JWT } = require('google-auth-library');
const creds = require('./credentials.json');

// --- CONFIGURATION ---
const TABS_TO_PROCESS = [
    '25x25mm',
    '40x40mm',
    '50x50mm',
    '50x150mm'
];

const TARGET_FLOOR = 0.2500;
const TARGET_AIM = 0.2510;
const COL_N = 13; // Variable
const COL_T = 19; // Margin
const COL_SKU = 0; // SKU (Col A) 
const COL_DESC = 5; // Description (Col F)

async function auditMultiBatch() {
    const auth = new JWT({
        email: creds.client_email,
        key: creds.private_key,
        scopes: ['https://www.googleapis.com/auth/spreadsheets'],
    });

    const doc = new GoogleSpreadsheet(process.env.SHEET_ID, auth);
    await doc.loadInfo();
    console.log(`Loaded Document: ${doc.title}`);

    for (const tabName of TABS_TO_PROCESS) {
        await auditTab(doc, tabName);
    }

    console.log("\n--- GLOBAL EXECUTION COMPLETE ---");
}

async function auditTab(doc, tabName) {
    const sheet = doc.sheetsByTitle[tabName];
    if (!sheet) {
        console.error(`\n[ERROR] Tab '${tabName}' NOT FOUND. Skipping.`);
        return;
    }

    console.log(`\n=== PROCESSING TAB: ${tabName} ===`);

    // Load range. 
    await sheet.loadCells('A1:T600');

    const fixedItems = [];
    let fixCount = 0;

    // Scan rows starting from Index 4 (Row 5)
    for (let r = 4; r < 600; r++) {
        const cellDesc = sheet.getCell(r, COL_DESC);
        if (!cellDesc.value) continue; // Skip empty rows

        const cellT = sheet.getCell(r, COL_T);
        if (typeof cellT.value !== 'number') continue;

        const currentMargin = cellT.value;

        // Tolerance < 24.99%
        if (currentMargin < TARGET_FLOOR - 0.0001) {
            const sku = sheet.getCell(r, COL_SKU).value;
            const desc = cellDesc.value;

            console.log(`[${tabName} FIXED] R${r + 1} | SKU:${sku} | Margin: ${(currentMargin * 100).toFixed(2)}%`);

            // Fix it
            const finalMargin = await solveRow(sheet, r);

            fixedItems.push({
                tab: tabName,
                row: r + 1,
                sku: sku,
                oldMargin: (currentMargin * 100).toFixed(2) + '%',
                newMargin: (finalMargin * 100).toFixed(2) + '%'
            });
            fixCount++;
        }
    }

    if (fixCount === 0) {
        console.log(`✅ ${tabName}: All Margins OK (>= 25%).`);
    } else {
        console.log(`⚠️ ${tabName}: Fixed ${fixCount} items.`);
        // console.table(fixedItems); // Too cluttered if many. Summary at end?
    }
}

async function solveRow(sheet, rowIndex) {
    const MAX_ITER = 20;
    let prevN = null; let prevT = null;

    for (let i = 0; i < MAX_ITER; i++) {
        await sheet.loadCells({
            startRowIndex: rowIndex, endRowIndex: rowIndex + 1,
            startColumnIndex: 0, endColumnIndex: 20
        });

        const cellN = sheet.getCell(rowIndex, COL_N);
        const cellT = sheet.getCell(rowIndex, COL_T);

        let currentN = typeof cellN.value === 'number' ? cellN.value : 0.5;
        let currentT = typeof cellT.value === 'number' ? cellT.value : 0;

        if (currentT >= TARGET_FLOOR && currentT <= (TARGET_FLOOR + 0.005)) {
            return currentT;
        }

        let newN;
        if (prevN === null) {
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

        if (newN === currentN || newN === prevN) {
            newN += 0.001 * (Math.random() > 0.5 ? 1 : -1);
        }

        if (newN < -0.9) newN = -0.5;

        prevN = currentN; prevT = currentT;

        cellN.value = Number(newN.toFixed(4));
        await sheet.saveUpdatedCells();
        await new Promise(r => setTimeout(r, 1200));
    }

    return sheet.getCell(rowIndex, COL_T).value;
}

auditMultiBatch();
