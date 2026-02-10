const fs = require('fs');

// Data embedded again for simplicity
const strData = `
[
  { "row": 75, "sku": 470, "metros": 0.5, "price": 14008, "margin": 0.3168 },
  { "row": 76, "sku": 475, "metros": 1, "price": 28192, "margin": 0.3080 },
  { "row": 77, "sku": 476, "metros": 3, "price": 57745, "margin": 0.1976 },
  { "row": 78, "sku": 477, "metros": 4, "price": 68210, "margin": 0.2279 },
  { "row": 79, "sku": 480, "metros": 2, "price": 48770, "margin": 0.2543 },
  { "row": 80, "sku": null, "metros": 2, "price": 57208, "margin": 0.2515 },
  { "row": 81, "sku": 485, "metros": 2.5, "price": 54355, "margin": 0.2662 },
  { "row": 82, "sku": 490, "metros": 3, "price": 65089, "margin": 0.2668 },
  { "row": 83, "sku": 490, "metros": 3, "price": 75585, "margin": 0.2626 },
  { "row": 84, "sku": 495, "metros": 3.5, "price": 73759, "margin": 0.2747 },
  { "row": 85, "sku": 500, "metros": 4, "price": 80389, "margin": 0.2675 },
  { "row": 86, "sku": 505, "metros": 5, "price": 99768, "margin": 0.2901 },
  { "row": 87, "sku": 505, "metros": 5, "price": 122828, "margin": 0.3030 },
  { "row": 88, "sku": 510, "metros": 6, "price": 110987, "margin": 0.2682 },
  { "row": 89, "sku": 510, "metros": 6, "price": 147111, "margin": 0.2698 },
  { "row": 90, "sku": 515, "metros": 6.5, "price": 119147, "margin": 0.2707 },
  { "row": 91, "sku": 520, "metros": 7, "price": 126041, "margin": 0.2985 },
  { "row": 92, "sku": 525, "metros": 8, "price": 144050, "margin": 0.2664 },
  { "row": 93, "sku": 530, "metros": 8, "price": 218595, "margin": 0.2512 },
  { "row": 94, "sku": 535, "metros": 9, "price": 155356, "margin": 0.2635 },
  { "row": 95, "sku": 540, "metros": 10, "price": 171165, "margin": 0.2657 },
  { "row": 96, "sku": 545, "metros": 10, "price": 222605, "margin": 0.2600 },
  { "row": 97, "sku": 550, "metros": 11, "price": 207686, "margin": 0.2409 },
  { "row": 98, "sku": 555, "metros": 12, "price": 217376, "margin": 0.2282 },
  { "row": 99, "sku": 560, "metros": 13, "price": 220649, "margin": 0.2680 },
  { "row": 100, "sku": 565, "metros": 14, "price": 236459, "margin": 0.2693 },
  { "row": 101, "sku": 570, "metros": 15, "price": 242028, "margin": 0.2614 },
  { "row": 102, "sku": 575, "metros": 16, "price": 263182, "margin": 0.2614 },
  { "row": 103, "sku": 580, "metros": 18, "price": 285461, "margin": 0.2659 },
  { "row": 104, "sku": 585, "metros": 20, "price": 317035, "margin": 0.2504 },
  { "row": 105, "sku": 590, "metros": 20, "price": 370045, "margin": 0.2503 },
  { "row": 106, "sku": 591, "metros": 20, "price": 414964, "margin": 0.2591 }
]
`;

const data = JSON.parse(strData);
const validRows = data.filter(d => d.sku !== null);

// Enforce Minimum 25% Margin ONLY
validRows.forEach(r => {
    r.originalPrice = r.price;
    r.originalMargin = r.margin;

    // Safety check for empty or invalid?
    if (r.price > 0 && r.margin < 0.25) {
        // P_new * 0.75 = P_old * (1 - r.margin)
        const pNew = (r.price * (1 - r.margin)) / 0.75;
        r.price = Math.ceil(pNew);
        r.margin = 0.25;
        r.adjusted = true;
    }
});

// Sort for display
validRows.sort((a, b) => a.metros - b.metros);

console.log("| SKU | Metros | Precio Actual/Sugerido | Margen | $/m | Cambio |");
console.log("|---|---|---|---|---|---|");

validRows.forEach(r => {
    const pm = (r.price / r.metros).toFixed(0);
    const mP = (r.margin * 100).toFixed(1) + '%';
    const note = r.adjusted ? `Subir a $${r.price.toLocaleString('es-AR')}` : 'OK';
    const priceDisplay = `$${r.price.toLocaleString('es-AR')}`;

    console.log(`| ${r.sku} | ${r.metros}m | ${priceDisplay} | ${mP} | $${pm} | ${note} |`);
});
