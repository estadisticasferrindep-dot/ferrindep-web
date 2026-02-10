/**
 * STOCK AUTOMATIZADO 2026 (FINAL)
 * Este script reemplaza al anterior y al sistema de Node.js.
 * - Ordena manuales en Negro/28.
 * - Sincroniza sobrantes en Azul/28.
 */

// ======================================================
// 1. CONFIGURACION
// ======================================================
const CFG = {
    START_ROW: 8,   // Bloque de stock empieza fila 8
    MAX_ROW: 100,   // Hasta fila 100
    PEDIDOS_TAB: 'PEDIDOS',
    // Definición de Columnas (1-based en Apps Script)
    COL: { A: 1, B: 2, C: 3, D: 4, E: 5, F: 6, G: 7, H: 8, I: 9, J: 10, K: 11, L: 12 },

    // Estilos
    STYLE_MANUAL: { size: 28, bold: true, color: '#000000' }, // Negro
    STYLE_AUTO: { size: 28, bold: true, color: '#0000FF' }, // Azul
    STYLE_RESET: { size: 28, bold: true, color: '#000000' } // Normal (Ahora igual a Manual: 28 + Negrita)
};

// ======================================================
// 2. TRIGGER MANUAL (Al editar Stock)
// ======================================================
function onEdit(e) {
    if (!e) return;
    const sheet = e.source.getActiveSheet();
    const name = sheet.getName();
    const r = e.range;

    // Si editan las hojas de stock (medidas)
    if (name.indexOf('mm') !== -1 && !name.includes('PEDIDOS')) {
        if (r.getRow() >= CFG.START_ROW && r.getColumn() <= CFG.COL.L) {
            // Ordenar columna editada
            const col = r.getColumn();
            const val = e.value; // Valor ingresado

            // Ordenar y pintar
            sortAndStyleColumn_(sheet, col, val, true);
        }
    }
}

// ======================================================
// 3. TRIGGER AUTOMATICO (Cada 1 minuto)
//    - Lee PEDIDOS
//    - Mueve sobrantes
//    - Ordena y Pinta AZUL
// ======================================================
function checkLeftovers() {
    const ss = SpreadsheetApp.getActiveSpreadsheet();
    const sheetOrders = ss.getSheetByName(CFG.PEDIDOS_TAB);
    if (!sheetOrders) return;

    const lastRow = 100; // Leer hasta fila 100
    // Leer bloque A24:Q100 (Índices: Fila 24, Col 1) -> Ahora leemos 17 columnas (A-Q)
    // GetValues devuelve matriz base-0
    const startRow = 24;
    const dataRange = sheetOrders.getRange(startRow, 1, lastRow - startRow + 1, 17);
    const data = dataRange.getValues();

    let updates = 0;

    for (let i = 0; i < data.length; i++) {
        const rowNum = startRow + i;
        const product = data[i][2];      // Col C (Index 2) - Antes estaba en 1 (ERROR)
        const thickness = data[i][3];    // Col D (Index 3) - Antes estaba en 2 (ERROR)
        const rawLeftover = data[i][10]; // Col K (Index 10)
        const processed = data[i][15];   // Col P (Index 15)
        const rawSku = data[i][16];      // Col Q (Index 16) - SKU

        // Parsear SKU a numero seguro
        let sku = null;
        if (typeof rawSku === 'number') sku = rawSku;
        else if (typeof rawSku === 'string') sku = parseInt(rawSku.trim());

        const leftover = cleanLeftover_(rawLeftover);

        if (leftover !== null && leftover > 0) {
            if (processed !== '✅') {
                Logger.log('Procesando Fila ' + rowNum + ': ' + product + ' | Sobrante: ' + leftover + ' | SKU: ' + sku);

                const target = getStockTarget_(product, thickness, sku);

                if (target) {
                    const stockSheet = ss.getSheetByName(target.tabName);
                    if (stockSheet) {
                        // 1. Leer columna destino actual
                        // 2. Agregar nuevo valor
                        // 3. Ordenar DESC
                        // 4. Escribir y formatear (AZUL para el nuevo)
                        sortAndStyleColumn_(stockSheet, target.colIndex, leftover, false);

                        // Marcar pedido
                        sheetOrders.getRange(rowNum, 16).setValue('✅'); // Col P
                        updates++;
                    }
                }
            }
        }
    }
}

// ======================================================
// 4. FUNCIONES DE LOGICA (CORE)
// ======================================================

// Función Core: Ordenar y Estilizar Columna (Preservando Colores)
function sortAndStyleColumn_(sheet, colIndex, targetVal, isManual) {
    var lastRow = sheet.getLastRow();
    // Si no hay datos suficientes, no hacemos nada (pero limpiamos si es necesario)
    if (lastRow < CFG.START_ROW) return;

    var numRows = CFG.MAX_ROW - CFG.START_ROW + 1;
    var range = sheet.getRange(CFG.START_ROW, colIndex, numRows, 1);

    // 1. Leer valores Y colores actuales para preservar formato
    var values = range.getValues();
    var colors = range.getFontColors();

    // 2. Construir lista de objetos {valor, color}
    var items = [];
    for (var i = 0; i < values.length; i++) {
        var val = values[i][0];
        if (typeof val === 'number' && !isNaN(val) && val !== '') {
            items.push({
                val: parseFloat(val),
                color: colors[i][0] || CFG.STYLE_MANUAL.color // Default a Negro si falla
            });
        }
    }

    // 3. Si es Automático (!isManual), agregamos el nuevo valor como AZUL
    if (!isManual && targetVal !== null) {
        items.push({
            val: parseFloat(targetVal),
            color: CFG.STYLE_AUTO.color // Azul
        });
    }

    // 4. Si es Manual (isManual), el valor YA ESTÁ en 'items' (leído de la hoja).
    //    Pero aseguremos que el valor editado (targetVal) sea NEGRO.
    //    (Esto corrige si el usuario sobrescribió una celda Azul).
    if (isManual && targetVal !== null) {
        // Buscamos coincidencia y forzamos negro. 
        // NOTA: Si hay duplicados, esto podría pintar ambos de negro. 
        // Asumimos que si el usuario toca el numero, confirma su existencia manual.
        for (var k = 0; k < items.length; k++) {
            if (Math.abs(items[k].val - targetVal) < 0.001) {
                items[k].color = CFG.STYLE_MANUAL.color;
            }
        }
    }

    // 5. Ordenar Descendente
    items.sort(function (a, b) { return b.val - a.val; });

    // 6. Preparar arrays de salida
    var newValues = [];
    var newColors = [];
    var newSizes = [];
    var newWeights = [];

    // Llenar con datos ordenados
    for (var j = 0; j < items.length; j++) {
        newValues.push([items[j].val]);
        newColors.push([items[j].color]);
        newSizes.push([CFG.STYLE_MANUAL.size]); // 28 siempre
        newWeights.push([CFG.STYLE_MANUAL.bold ? 'bold' : 'normal']); // Bold siempre
    }

    // Rellenar celdas vacías hasta completar el rango
    while (newValues.length < numRows) {
        newValues.push(['']);
        newColors.push(['#000000']); // Color irrelevante en celda vacía
        newSizes.push([CFG.STYLE_MANUAL.size]);
        newWeights.push(['normal']);
    }

    // 7. Escribir todo de una sola vez (Batch Update)
    range.setValues(newValues);
    range.setFontColors(newColors);
    range.setFontSizes(newSizes);
    range.setFontWeights(newWeights);

    // Alineación siempre centro
    range.setHorizontalAlignment("center");
    range.setVerticalAlignment("middle");
}


function cleanLeftover_(val) {
    if (typeof val !== 'number') return null;
    return Math.floor(val * 2) / 2; // Redondear a 0.5
}

function normalize(s) {
    return String(s || '').toLowerCase().replace(',', '.').replace(/\s+/g, '');
}

function getStockTarget_(title, thicknessRaw, sku) {
    const t = normalize(title);
    const th = normalize(thicknessRaw);
    const C = CFG.COL;

    // ----------------------------------------------------
    // PRIORIDAD: CAMBIOS POR SKU (Rango 1675 - 1780)
    // ----------------------------------------------------
    if (sku && typeof sku === 'number') {
        // [NUEVO] Mapeo SKUs -> 50x50mm 2mm (Col B, E, F, G, H)

        // Col B (50cm)
        const skus50cm = [3255, 3275, 3280, 3285, 3295, 3305, 3306, 3315, 3330, 3345, 3346];
        if (skus50cm.includes(sku)) return { tabName: '50x50mm 2mm', colIndex: C.B };

        // Col E (75cm)
        const skus75cm = [3350, 3355, 3360, 3370, 3375, 3380, 3385, 3390, 3395, 3400, 3405, 3410, 3415, 3420, 3435, 3440, 3445, 3450];
        if (skus75cm.includes(sku)) return { tabName: '50x50mm 2mm', colIndex: C.E };

        // Col F (1m)
        const skus1m = [3455, 3460, 3470, 3475, 3480, 3485, 3495, 3505, 3506, 3510, 3515, 3520, 3525, 3530, 3535, 3545, 3546, 3547];
        if (skus1m.includes(sku)) return { tabName: '50x50mm 2mm', colIndex: C.F };

        // Col G (1.20m)
        const skus120 = [3550, 3551, 3560, 3565, 3570, 3575, 3580, 3585, 3595, 3596, 3605, 3610, 3620, 3622, 3625, 3635, 3636];
        if (skus120.includes(sku)) return { tabName: '50x50mm 2mm', colIndex: C.G };

        // Col H (1.50m)
        const skus150 = [3641, 3647, 3655, 3659, 3661, 3664, 3665, 3669, 3670, 3671, 3679, 3682, 3690, 3699, 3701, 3714, 3729];
        if (skus150.includes(sku)) return { tabName: '50x50mm 2mm', colIndex: C.H };

        // [NUEVO] Mapeo SKUs -> 10x10mm (Col G)
        const skus10x10G = [470, 475, 476, 477, 480, 485, 490, 495, 500, 505, 510, 515, 520, 525, 530, 535, 540, 545, 550, 555, 560, 565, 570, 575, 580, 585, 590, 591];
        if (skus10x10G.includes(sku)) return { tabName: '10x10mm', colIndex: C.G };

        // [NUEVO] Mapeo SKUs -> 18x40mm (Col E)
        const skus18x40E = [5845, 5846, 5847, 5849, 5851, 5852, 5853, 5855, 5857, 5859, 5861, 5865, 5869, 5873, 5874, 5879, 5883];
        if (skus18x40E.includes(sku)) return { tabName: '18x40mm', colIndex: C.E };

        // [NUEVO] Mapeo SKUs -> 18x40mm (Col F)
        const skus18x40F = [5885, 5887, 5889, 5891, 5893, 5894, 5898, 5901, 5905, 5906, 5909, 5913, 5917, 5919, 5921, 5923];
        if (skus18x40F.includes(sku)) return { tabName: '18x40mm', colIndex: C.F };


        // 25x25 0.9mm 10mts (SKU 1675 - 1780) -> Col E
        if (sku >= 1675 && sku <= 1780) {
            return { tabName: '25x25mm 0,9mm', colIndex: C.E };
        }
        // 8x17 1m (SKU 5125 - 5210) -> Col F
        if (sku >= 5125 && sku <= 5210) {
            return { tabName: '8x17mm', colIndex: C.F };
        }
    }

    if (t.includes('10x10')) {
        if (t.includes('19cm')) return { tabName: '10x10mm', colIndex: C.B };
        if (t.includes('30cm')) return { tabName: '10x10mm', colIndex: C.C };
        if (t.includes('40cm')) return { tabName: '10x10mm', colIndex: C.D };
        if (t.includes('50cm')) return { tabName: '10x10mm', colIndex: C.E };
        if (t.includes('60cm')) return { tabName: '10x10mm', colIndex: C.F };
        if (t.includes('1m') || t.includes('1mt')) return { tabName: '10x10mm', colIndex: C.G };
    }

    if (t.includes('15x15')) {
        if (t.includes('30cm')) return { tabName: '15x15mm', colIndex: C.B };
        if (t.includes('40cm')) return { tabName: '15x15mm', colIndex: C.C };
        if (t.includes('50cm')) return { tabName: '15x15mm', colIndex: C.D };
        if (t.includes('60cm')) return { tabName: '15x15mm', colIndex: C.E };
        if (t.includes('75cm')) return { tabName: '15x15mm', colIndex: C.F };
        if (t.includes('1.5m') || t.includes('1.50m')) return { tabName: '15x15mm', colIndex: C.I };
        if (t.includes('1.2') || t.includes('1.20')) return { tabName: '15x15mm', colIndex: C.H };
        if (t.includes('1m') || t.includes('1mt')) return { tabName: '15x15mm', colIndex: C.G };
    }

    if (t.includes('20x20')) {
        if (th.includes('0.9')) {
            if (t.includes('50cm')) return { tabName: '20x20mm', colIndex: C.B };
            if (t.includes('1m') || t.includes('1mt')) return { tabName: '20x20mm', colIndex: C.C };
        }
        if (th.includes('1.2')) {
            if (t.includes('50cm')) return { tabName: '20x20mm', colIndex: C.E };
            if (t.includes('1m') || t.includes('1mt')) return { tabName: '20x20mm', colIndex: C.F };
        }
    }

    if (t.includes('25x25')) {
        if (th.includes('0.9')) {
            if (t.includes('48cm') || t.includes('50cm')) return { tabName: '25x25mm 0,9mm', colIndex: C.B };
            if (t.includes('1m') || t.includes('1mt')) return { tabName: '25x25mm 0,9mm', colIndex: C.E };
            if (t.includes('10m') || t.includes('10mt')) return { tabName: '25x25mm 0,9mm', colIndex: C.E }; // Fix for 10mts rolls
        }
        if (th.includes('1.5') || th.includes('1.6')) {
            const tabName = '25x25mm 1,5mm / 2,1mm';
            if (t.includes('25cm')) return { tabName, colIndex: C.B };
            if (t.includes('30cm')) return { tabName, colIndex: C.C };
            if (t.includes('40cm')) return { tabName, colIndex: C.D };
            if (t.includes('50cm')) return { tabName, colIndex: C.E };
            if (t.includes('60cm')) return { tabName, colIndex: C.F };
            if (t.includes('75cm')) return { tabName, colIndex: C.G };
            if (t.includes('1.5m')) return { tabName, colIndex: C.J };
            if (t.includes('1.2') || t.includes('1.20')) return { tabName, colIndex: C.I };
            if (t.includes('1m') || t.includes('1mt')) return { tabName, colIndex: C.H };
        }
        if (th.includes('2.1')) {
            const tabName = '25x25mm 1,5mm / 2,1mm';
            if (t.includes('1m') || t.includes('1mt')) return { tabName, colIndex: C.K };
        }
    }

    if (t.includes('40x40') || t.includes('38x38')) {
        if (t.includes('negro')) return { tabName: '40x40mm', colIndex: C.H };
        if (t.includes('50cm')) return { tabName: '40x40mm', colIndex: C.B };
        if (t.includes('1m') || t.includes('1mt')) return { tabName: '40x40mm', colIndex: C.C };
    }

    if (t.includes('50x50')) {
        if (th.includes('1.6')) {
            if (t.includes('50cm')) return { tabName: '50x50mm 1,6mm', colIndex: C.B };
            if (t.includes('1m') || t.includes('1mt')) return { tabName: '50x50mm 1,6mm', colIndex: C.C };
        }
        if (th.includes('2.1')) {
            if (t.includes('50cm')) return { tabName: '50x50mm 2,1 - 2,5mm', colIndex: C.B };
            if (t.includes('1m') || t.includes('1mt')) return { tabName: '50x50mm 2,1 - 2,5mm', colIndex: C.D };
        }
        if (th.includes('2.5')) {
            if (t.includes('50cm')) return { tabName: '50x50mm 2,1 - 2,5mm', colIndex: C.G };
            if (t.includes('1m') || t.includes('1mt')) return { tabName: '50x50mm 2,1 - 2,5mm', colIndex: C.I };
        }
        if (th.includes('1.9') || (th.includes('2') && !th.includes('2.1') && !th.includes('2.5'))) {
            const tabName = '50x50mm 2mm';
            if (t.includes('50cm')) return { tabName, colIndex: C.B };
            if (t.includes('60cm')) return { tabName, colIndex: C.C };
            if (t.includes('70cm')) return { tabName, colIndex: C.D };
            if (t.includes('75cm')) return { tabName, colIndex: C.E };
            if (t.includes('1m') || t.includes('1mt')) return { tabName, colIndex: C.F };
            if (t.includes('1.2')) return { tabName, colIndex: C.G };
            if (t.includes('1.5')) return { tabName, colIndex: C.H };
            if (t.includes('2m')) return { tabName, colIndex: C.I };
        }
    }

    if (t.includes('50x150')) {
        if (t.includes('60cm')) return { tabName: '50x150mm', colIndex: C.B };
        if (t.includes('1m') || t.includes('1mt')) return { tabName: '50x150mm', colIndex: C.D };
        if (t.includes('1.5')) return { tabName: '50x150mm', colIndex: C.G };
        if (t.includes('2m')) return { tabName: '50x150mm', colIndex: C.I };
    }

    if (t.includes('8x17')) {
        if (t.includes('50cm')) return { tabName: '8x17mm', colIndex: C.B };
        if (t.includes('60cm')) return { tabName: '8x17mm', colIndex: C.D };
        if (t.includes('1m') || t.includes('1mt')) return { tabName: '8x17mm', colIndex: C.F };
        if (t.includes('1.2')) return { tabName: '8x17mm', colIndex: C.H };
    }

    if (t.includes('18x40')) {
        if (t.includes('30cm')) return { tabName: '18x40mm', colIndex: C.B };
        if (t.includes('60cm')) return { tabName: '18x40mm', colIndex: C.D };
        if (t.includes('70cm')) return { tabName: '18x40mm', colIndex: C.E };
        if (t.includes('80cm')) return { tabName: '18x40mm', colIndex: C.F };
        if (t.includes('1.5')) return { tabName: '18x40mm', colIndex: C.I };
        if (t.includes('1.2')) return { tabName: '18x40mm', colIndex: C.H };
        if (t.includes('1m') || t.includes('1mt')) return { tabName: '18x40mm', colIndex: C.G };
    }

    if (t.includes('20x50')) {
        if (t.includes('1m') || t.includes('1mt')) return { tabName: '20x50mm', colIndex: C.B };
        if (t.includes('1.5')) return { tabName: '20x50mm', colIndex: C.D };
    }

    return null;
}
