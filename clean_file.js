const fs = require('fs');
const path = require('path');

const filesToClean = [
    path.join(__dirname, 'app', 'Http', 'Controllers', 'WebController.php'),
    path.join(__dirname, 'app', 'Http', 'Controllers', 'CarritoController.php'),
    path.join(__dirname, 'app', 'Http', 'Controllers', 'ShippingCalculatorController.php'),
    path.join(__dirname, 'app', 'Models', 'Producto.php')
];

filesToClean.forEach(filePath => {
    try {
        if (fs.existsSync(filePath)) {
            let content = fs.readFileSync(filePath, 'utf8');
            let modified = false;

            // Remove BOM
            if (content.charCodeAt(0) === 0xFEFF) {
                content = content.slice(1);
                console.log(`[${path.basename(filePath)}] Removed BOM.`);
                modified = true;
            }

            // Normalize Line Endings to LF
            if (content.includes('\r\n')) {
                content = content.replace(/\r\n/g, '\n').replace(/\r/g, '\n');
                console.log(`[${path.basename(filePath)}] Normalized line endings to LF.`);
                modified = true;
            }

            if (modified) {
                fs.writeFileSync(filePath, content, 'utf8');
                console.log(`[${path.basename(filePath)}] File sanitized.`);
            } else {
                console.log(`[${path.basename(filePath)}] Already clean.`);
            }
        } else {
            console.log(`[${path.basename(filePath)}] Not found locally.`);
        }
    } catch (err) {
        console.error(`Error processing ${filePath}:`, err);
    }
});
