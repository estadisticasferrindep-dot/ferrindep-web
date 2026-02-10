const { exec } = require('child_process');

console.log("--- ANTI-GRAVITY STOCK WATCHER ---");
console.log("Running sync-stock.js every 60 seconds...");
console.log("Keep this window open to maintain automation.\n");

function runSync() {
    console.log(`[${new Date().toLocaleTimeString()}] Checking for new leftovers...`);

    exec('node sync-stock.js', (error, stdout, stderr) => {
        if (error) {
            console.error(`Error: ${error.message}`);
            return;
        }
        if (stderr) {
            // Ignore benign warnings
            if (!stderr.includes('DeprecationWarning')) console.error(`Stderr: ${stderr}`);
        }

        // Filter output to show only relevant actions
        const lines = stdout.split('\n');
        const relevant = lines.filter(l =>
            l.includes('Processing') ||
            l.includes('Sending') ||
            l.includes('Wrote') ||
            l.includes('Error')
        );

        if (relevant.length > 0) {
            console.log(relevant.join('\n'));
        } else {
            // console.log("  (Nothing new)");
        }
    });
}

// Run immediately
runSync();

// Loop every 60 seconds (60000 ms)
setInterval(runSync, 60000);
