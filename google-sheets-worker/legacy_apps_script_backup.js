/************************************************************
 * STOCK NUEVO – v4.7 (CORTEZ BACKUP)
 * Copied from User Chat on 2026-01-29
 * This script was running on Google Sheets to auto-sort and color stock.
 ************************************************************/

/* ===== Normalizador fuerte de nombres de hoja ===== */
function SN_norm_(s) {
    return String(s || '')
        .replace(/\u00A0/g, ' ')                     // NBSP → espacio normal
        .replace(/\u00D7/g, 'x')                     // símbolo × → x
        .replace(/[\u2012\u2013\u2014\u2212]/g, '-') // guiones raros → "-"
        .replace(/\s*-\s*/g, '-')                    // quita espacios alrededor del guion
        .replace(/\s+/g, ' ')                        // colapsa espacios
        .trim()
        .toLowerCase();                              // case-insensitive
}

/* ===== Config ===== */
const CFG = {
    SHEETS: new Set([
        SN_norm_('10x10mm'),
        SN_norm_('15x15mm'),
        SN_norm_('20x20mm'),
        SN_norm_('25x25mm 0,9mm'),
        SN_norm_('25x25mm 1,5mm / 2,1mm'),
        SN_norm_('40x40mm'),
        SN_norm_('50x50mm 1,6mm'),
        SN_norm_('50x50mm 2mm'),
        SN_norm_('50x50mm 2,1 - 2,5mm'),
        SN_norm_('50x150mm'),
        SN_norm_('8x17mm'),
        SN_norm_('18x40mm'),
        SN_norm_('20x50mm')
    ]),
    START_ROW: 8,     // bloque ordenado/validado (filas)
    END_ROW: 100,
    START_COL: 1,     // A
    END_COL: 11,      // K
    HEADER_ROW: 4,    // si hay título acá, fila 7 no puede quedar vacía
    MUST_ROW: 7,      // fila obligatoria (número, puede ser 0)
    FONT_ORANGE: '#FFA500',
    BG_AMBER: '#FFF2CC', // ámbar suave
    VALIDATION_SHEET: '_LISTAS_VALIDACION',
    DISPLAY_FORMAT: '0.#'  // 1 ó 1,5 (no 1,50)
};

// ... (Rest of logic omitted for brevity, full logic in memory)
// Main Logic: SN_sortColumnOnly_(sh, col, editedRaw) -> Sorts DESC and marks.
