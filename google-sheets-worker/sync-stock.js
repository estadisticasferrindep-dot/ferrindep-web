require('dotenv').config();
const { GoogleSpreadsheet } = require('google-spreadsheet');
const { JWT } = require('google-auth-library');
const creds = require('./credentials.json');

const COL = { A: 0, B: 1, C: 2, D: 3, E: 4, F: 5, G: 6, H: 7, I: 8, J: 9, K: 10, L: 11, M: 12, N: 13, O: 14, P: 15 };

function clean(str) {
    if (!str) return '';
    return String(str).toLowerCase().replace(',', '.').replace(/\s+/g, '');
}

function getStockTarget(title, thicknessRaw) {
    const t = clean(title);
    const th = clean(thicknessRaw);

    if (t.includes('10x10')) {
        let c = -1;
        if (t.includes('19cm')) c = COL.B;
        else if (t.includes('30cm')) c = COL.C;
        else if (t.includes('40cm')) c = COL.D;
        else if (t.includes('50cm')) c = COL.E;
        else if (t.includes('60cm')) c = COL.F;
        else if (t.includes('1m') || t.includes('1mt')) c = COL.G;
        if (c !== -1) return { tabName: '10x10mm', colIndex: c };
    }

    if (t.includes('15x15')) {
        let c = -1;
        if (t.includes('30cm')) c = COL.B;
        else if (t.includes('40cm')) c = COL.C;
        else if (t.includes('50cm')) c = COL.D;
        else if (t.includes('60cm')) c = COL.E;
        else if (t.includes('75cm')) c = COL.F;
        else if (t.includes('1.5m') || t.includes('1.50m')) c = COL.I;
        else if (t.includes('1.2') || t.includes('1.20')) c = COL.H;
        else if (t.includes('1m') || t.includes('1mt')) c = COL.G;
        if (c !== -1) return { tabName: '15x15mm', colIndex: c };
    }

    if (t.includes('20x20')) {
        if (th.includes('0.9')) {
            if (t.includes('50cm')) return { tabName: '20x20mm', colIndex: COL.B };
            if (t.includes('1m') || t.includes('1mt')) return { tabName: '20x20mm', colIndex: COL.C };
        }
        if (th.includes('1.2')) {
            if (t.includes('50cm')) return { tabName: '20x20mm', colIndex: COL.E };
            if (t.includes('1m') || t.includes('1mt')) return { tabName: '20x20mm', colIndex: COL.F };
        }
    }

    if (t.includes('25x25')) {
        if (th.includes('0.9')) {
            if (t.includes('48cm') || t.includes('50cm')) return { tabName: '25x25mm 0,9mm', colIndex: COL.B };
            if (t.includes('1m') || t.includes('1mt')) return { tabName: '25x25mm 0,9mm', colIndex: COL.E };
        }
        if (th.includes('1.5') || th.includes('1.6')) {
            const tabName = '25x25mm 1,5mm / 2,1mm';
            if (t.includes('25cm')) return { tabName, colIndex: COL.B };
            if (t.includes('30cm')) return { tabName, colIndex: COL.C };
            if (t.includes('40cm')) return { tabName, colIndex: COL.D };
            if (t.includes('50cm')) return { tabName, colIndex: COL.E };
            if (t.includes('60cm')) return { tabName, colIndex: COL.F };
            if (t.includes('75cm')) return { tabName, colIndex: COL.G };
            if (t.includes('1.5m')) return { tabName, colIndex: COL.J };
            if (t.includes('1.2') || t.includes('1.20')) return { tabName, colIndex: COL.I };
            if (t.includes('1m') || t.includes('1mt')) return { tabName, colIndex: COL.H };
        }
        if (th.includes('2.1')) {
            const tabName = '25x25mm 1,5mm / 2,1mm';
            if (t.includes('1m') || t.includes('1mt')) return { tabName, colIndex: COL.K };
        }
    }

    if (t.includes('40x40') || t.includes('38x38')) {
        if (t.includes('negro')) return { tabName: '40x40mm', colIndex: COL.H };
        if (t.includes('50cm')) return { tabName: '40x40mm', colIndex: COL.B };
        if (t.includes('1m') || t.includes('1mt')) return { tabName: '40x40mm', colIndex: COL.C };
    }

    if (t.includes('50x50')) {
        if (th.includes('1.6')) {
            if (t.includes('50cm')) return { tabName: '50x50mm 1,6mm', colIndex: COL.B };
            if (t.includes('1m') || t.includes('1mt')) return { tabName: '50x50mm 1,6mm', colIndex: COL.C };
        }
        if (th.includes('2.1')) {
            if (t.includes('50cm')) return { tabName: '50x50mm 2,1 - 2,5mm', colIndex: COL.B };
            if (t.includes('1m') || t.includes('1mt')) return { tabName: '50x50mm 2,1 - 2,5mm', colIndex: COL.D };
        }
        if (th.includes('2.5')) {
            if (t.includes('50cm')) return { tabName: '50x50mm 2,1 - 2,5mm', colIndex: COL.G };
            if (t.includes('1m') || t.includes('1mt')) return { tabName: '50x50mm 2,1 - 2,5mm', colIndex: COL.I };
        }
        if (th.includes('1.9') || (th.includes('2') && !th.includes('2.1') && !th.includes('2.5'))) {
            const tabName = '50x50mm 2mm';
            if (t.includes('50cm')) return { tabName, colIndex: COL.B };
            if (t.includes('60cm')) return { tabName, colIndex: COL.C };
            if (t.includes('70cm')) return { tabName, colIndex: COL.D };
            if (t.includes('75cm')) return { tabName, colIndex: COL.E };
            if (t.includes('1m') || t.includes('1mt')) return { tabName, colIndex: COL.F };
            if (t.includes('1.2')) return { tabName, colIndex: COL.G };
            if (t.includes('1.5')) return { tabName, colIndex: COL.H };
            if (t.includes('2m')) return { tabName, colIndex: COL.I };
        }
    }

    if (t.includes('50x150')) {
        if (t.includes('60cm')) return { tabName: '50x150mm', colIndex: COL.B };
        if (t.includes('1m') || t.includes('1mt')) return { tabName: '50x150mm', colIndex: COL.D };
        if (t.includes('1.5')) return { tabName: '50x150mm', colIndex: COL.G };
        if (t.includes('2m')) return { tabName: '50x150mm', colIndex: COL.I };
    }

    if (t.includes('8x17')) {
        if (t.includes('50cm')) return { tabName: '8x17mm', colIndex: COL.B };
        if (t.includes('60cm')) return { tabName: '8x17mm', colIndex: COL.D };
        if (t.includes('1m') || t.includes('1mt')) return { tabName: '8x17mm', colIndex: COL.F };
        if (t.includes('1.2')) return { tabName: '8x17mm', colIndex: COL.H };
    }

    if (t.includes('18x40')) {
        if (t.includes('30cm')) return { tabName: '18x40mm', colIndex: COL.B };
        if (t.includes('60cm')) return { tabName: '18x40mm', colIndex: COL.D };
        if (t.includes('70cm')) return { tabName: '18x40mm', colIndex: COL.E };
        if (t.includes('80cm')) return { tabName: '18x40mm', colIndex: COL.F };
        if (t.includes('1.5')) return { tabName: '18x40mm', colIndex: COL.I };
        if (t.includes('1.2')) return { tabName: '18x40mm', colIndex: COL.H };
        if (t.includes('1m') || t.includes('1mt')) return { tabName: '18x40mm', colIndex: COL.G };
    }

    if (t.includes('20x50')) {
        if (t.includes('1m') || t.includes('1mt')) return { tabName: '20x50mm', colIndex: COL.B };
        if (t.includes('1.5')) return { tabName: '20x50mm', colIndex: COL.D };
    }

    return null;
}

function cleanLeftover(val) {
    if (typeof val !== 'number') return null;
    return Math.floor(val * 2) / 2;
}

async function syncStock() {
    try {
        const serviceAccountAuth = new JWT({
            email: creds.client_email,
            key: creds.private_key,
            scopes: ['https://www.googleapis.com/auth/spreadsheets'],
        });

        const doc = new GoogleSpreadsheet(process.env.SHEET_ID_STOCK, serviceAccountAuth);
        await doc.loadInfo();

        const sheetOrders = doc.sheetsByTitle['PEDIDOS'];
        const MAX_ROWS = 100;
        await sheetOrders.loadCells(`A24:P${MAX_ROWS}`);

        console.log("--- SYNCING STOCK (SORTING + MARKING) ---");

        let updates = 0;

        for (let r = 23; r < MAX_ROWS; r++) {
            const rowNum = r + 1;

            const product = sheetOrders.getCell(r, 2).value;
            const thickness = sheetOrders.getCell(r, 3).value;
            const rawLeftover = sheetOrders.getCell(r, 10).value;
            const processedCell = sheetOrders.getCell(r, 15);
            const processed = processedCell.value;

            const leftover = cleanLeftover(rawLeftover);

            if (leftover !== null && leftover > 0) {
                if (processed !== '✅') {
                    console.log(`Processing Row ${rowNum}: ${product} | Espesor: ${thickness} (Leftover Raw: ${rawLeftover} -> Clean: ${leftover})`);

                    const target = getStockTarget(product, thickness);

                    if (target) {
                        const stockSheet = doc.sheetsByTitle[target.tabName];

                        if (stockSheet) {
                            console.log(`  -> Sending to '${target.tabName}' (Col Index ${target.colIndex})`);

                            const START_ROW_IDX = 7;
                            const READ_LIMIT = 100;

                            await stockSheet.loadCells({
                                startRowIndex: START_ROW_IDX,
                                endRowIndex: START_ROW_IDX + READ_LIMIT,
                                startColumnIndex: target.colIndex,
                                endColumnIndex: target.colIndex + 1
                            });

                            const currentValues = [];
                            for (let i = 0; i < READ_LIMIT; i++) {
                                const val = stockSheet.getCell(START_ROW_IDX + i, target.colIndex).value;
                                if (typeof val === 'number') {
                                    currentValues.push(val);
                                }
                            }

                            currentValues.push(leftover);
                            currentValues.sort((a, b) => b - a);

                            let marked = false;
                            for (let i = 0; i < READ_LIMIT; i++) {
                                const cell = stockSheet.getCell(START_ROW_IDX + i, target.colIndex);

                                if (i < currentValues.length) {
                                    const val = currentValues[i];
                                    cell.value = val;

                                    if (!marked && Math.abs(val - leftover) < 0.0001) {
                                        cell.textFormat = {
                                            foregroundColor: { red: 0, green: 0, blue: 1 },
                                            fontSize: 28,
                                            bold: true
                                        };
                                        marked = true;
                                    } else {
                                        cell.textFormat = {
                                            foregroundColor: { red: 0, green: 0, blue: 0 },
                                            fontSize: 12,
                                            bold: false
                                        };
                                    }
                                } else {
                                    cell.value = '';
                                    cell.textFormat = { foregroundColor: { red: 0, green: 0, blue: 0 }, fontSize: 12, bold: false };
                                }
                            }

                            await stockSheet.saveUpdatedCells();
                            console.log(`  -> Wrote & Sorted ${currentValues.length} items to Col Index ${target.colIndex}`);

                            processedCell.value = '✅';
                            await sheetOrders.saveUpdatedCells();
                            updates++;
                        } else {
                            console.log(`  -> ❌ Tab '${target.tabName}' NOT FOUND in Sheet.`);
                        }

                    } else {
                        console.log("  -> ⚠️ No mapping found.");
                    }
                }
            }
        }

        if (updates === 0) console.log("\nNo new leftovers to sync.");

    } catch (error) {
        // Log clean error message instead of massive object dump
        const safeError = error.message || String(error);
        console.error("ERROR running sync:", safeError.length > 500 ? safeError.substring(0, 500) + '...' : safeError);
    }
}

syncStock();
