require('dotenv').config();
const { GoogleSpreadsheet } = require('google-spreadsheet');
const { JWT } = require('google-auth-library');
const creds = require('./credentials.json');

// Constants for calculation (if needed for margin check, though we might just read T)
// For T calculation we need formulas, but user said "Margin (col T) no puede dar menos que 25%"
// So we probably need to reverse engineer the Price required for T=25% if current is lower.
// T = (dL + y - C) / D  <-- From previous turn.
// or simply we read current T and if < 25%, we calculate Price for T=25%.

async function analyze() {
    const serviceAccountAuth = new JWT({
        email: creds.client_email,
        key: creds.private_key,
        scopes: ['https://www.googleapis.com/auth/spreadsheets'],
    });

    const doc = new GoogleSpreadsheet(process.env.SHEET_ID, serviceAccountAuth);
    await doc.loadInfo();
    const sheet = doc.sheetsByTitle['10x10mm']; // Note title

    if (!sheet) { console.error("Sheet 10x10mm not found"); process.exit(1); }

    // Rows 75 to 106. Indices 74 to 105.
    // Columns: A(SKU), B(Metros), C(Costo), D(PrecioVenta), T(Margen)
    // Also need parameters for calc if we need to adjust? 
    // Probably column N, O, etc play a role.
    // But maybe the user just wants the Output Price (D) adjusted.
    // Wait, D is usually an INPUT or FORMULA?
    // In previous sheet, D was calculated from N. =ROUND( ((C*(1+N)/Z8) - I + Z10) / (Z4+Z6-1) )
    // If D is a formula, we can't "set" D directly without changing N.
    // User asked: "Quisiera que me des aquí todos los precios de venta" (I give you the list).
    // "No toques nada de la hoja".
    // So I just calculate the *Ideal D*.

    await sheet.loadCells('A75:T106');

    // We also need constants if we want to validly calculate the connection between Price and Margin?
    // Or simpler: We trust the current Formula for T = O/D ?
    // If we change Price (D), T changes. 
    // T = (NetRevenue - Cost) / Price?
    // Let's first read the values to see what we have.

    const data = [];
    for (let i = 74; i <= 105; i++) {
        data.push({
            row: i + 1,
            sku: sheet.getCell(i, 0).value,
            metros: sheet.getCell(i, 1).value,
            cost: sheet.getCell(i, 2).value,
            price: sheet.getCell(i, 3).value,
            margin: sheet.getCell(i, 19).value,
            // formulas
            priceFormula: sheet.getCell(i, 3).formula,
            marginFormula: sheet.getCell(i, 19).formula
        });
    }

    console.log(JSON.stringify(data, null, 2));
}

analyze();
