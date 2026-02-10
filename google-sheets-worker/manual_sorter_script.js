/**
 * STOCK MANUAL SORTER (Simple)
 * Detecta edición manual -> Ordena -> Pone en Negro, Negrita y Grande (28).
 */

const TARGET_SHEETS = [
    '10x10mm', '15x15mm', '20x20mm', '25x25mm 0,9mm', '25x25mm 1,5mm / 2,1mm',
    '40x40mm', '50x50mm 1,6mm', '50x50mm 2mm', '50x50mm 2,1 - 2,5mm', '50x150mm',
    '8x17mm', '18x40mm', '20x50mm'
];

function onEdit(e) {
    if (!e || !e.range) return;
    const sheet = e.range.getSheet();

    // Solo correr en hojas de Stock
    const name = sheet.getName().replace(/\s/g, '').toLowerCase();
    const isTarget = TARGET_SHEETS.some(t => t.replace(/\s/g, '').toLowerCase() === name);
    if (!isTarget) return;

    const row = e.range.getRow();
    const col = e.range.getColumn();

    // Solo desde Fila 8 para abajo
    if (row < 8) return;

    // 1. FORZAR ESTILO MANUAL (Negro, Negrita, Grande)
    e.range.setFontColor('black')
        .setFontWeight('bold')   // Negrita
        .setFontSize(28)         // Grande (28)
        .setFontLine('none');

    // 2. ORDENAR COLUMNA (Descendente)
    const lastRow = sheet.getMaxRows();
    const colRange = sheet.getRange(8, col, lastRow - 7, 1);
    colRange.sort({ column: col, ascending: false });
}
