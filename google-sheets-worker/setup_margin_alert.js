require('dotenv').config();
const { GoogleSpreadsheet } = require('google-spreadsheet');
const { JWT } = require('google-auth-library');
const creds = require('./credentials.json');

const TARGET_TAB_NAME = 'Hoja1';

async function setupAlert() {
    const jwt = new JWT({
        email: creds.client_email,
        key: creds.private_key,
        scopes: ['https://www.googleapis.com/auth/spreadsheets'],
    });

    const doc = new GoogleSpreadsheet(process.env.SHEET_ID, jwt);
    await doc.loadInfo();

    const sheet = doc.sheetsByTitle[TARGET_TAB_NAME];
    if (!sheet) {
        console.error('Sheet not found');
        return;
    }

    console.log(`Loading Hoja1 cells (N1, N2)...`);
    // Load N1 (Row 0, Col 13) and N2 (Row 1, Col 13)
    // A=0,... M=12, N=13
    await sheet.loadCells('N1:N2');

    const cellN1 = sheet.getCell(0, 13);
    const cellN2 = sheet.getCell(1, 13);

    // N1: User input threshold. Default 0.245 (24.5%)
    // The user inputs "24,5" in the UI, which Sheets interprets as number if properly formatted.
    // I will set it as a raw number 0.245.
    // If user changes it later, formula in N2 will update.
    console.log("Setting N1 to 0.245 (24.5%)");
    cellN1.value = 0.245;
    cellN1.numberFormat = { type: 'PERCENT', pattern: '0.00%' }; // Format as %

    // N2: Formula
    // =COUNTIF(K:K, "<" & N1)
    // Important: Google Sheets API usually expects US syntax (comma) for formulas.
    // The UI displays it localized (semicolon).
    // Exclude 0/Empty (which are < 24.5%). Use COUNTIFS.
    // Spanish locale uses semicolon.
    const formula = '=COUNTIFS(K:K; "<" & N1; K:K; ">0")';
    console.log(`Setting N2 to formula: ${formula}`);
    cellN2.formula = formula;

    // Optional: Add labels in M1/M2? User didn't ask, but it's polite.
    // User said "en celda N1... y que celda N2 me diga".
    // I'll stick to N1/N2. Maybe add Note?
    cellN1.note = "Ingrese el margen límite aquí (ej: 24,5%)";
    cellN2.note = "Cantidad de items por debajo del límite";

    await sheet.saveUpdatedCells();
    console.log("Alert setup complete.");
}

setupAlert().catch(console.error);
