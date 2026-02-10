const ftp = require("basic-ftp");
const fs = require("fs");
const path = require("path");

// Load FTP configuration
let config;
try {
    const configPath = path.join(__dirname, ".vscode", "sftp.json");
    if (fs.existsSync(configPath)) {
        const configFile = fs.readFileSync(configPath, 'utf8');
        // Remove comments from JSON if present
        const jsonContent = configFile.replace(/\/\/.*$/gm, '');
        config = JSON.parse(jsonContent);
    } else {
        console.error("Config file not found at " + configPath);
        process.exit(1);
    }
} catch (e) {
    console.error("Error parsing config:", e);
    process.exit(1);
}

async function verifyLayout() {
    const client = new ftp.Client();
    // client.ftp.verbose = true;

    try {
        await client.access({
            host: config.host,
            user: config.username,
            password: config.password,
            secure: false
        });

        console.log("Connected.");

        const remotePath = "/ferrindep/resources/views/layouts/plantilla.blade.php";
        const localPath = "temp_remote_plantilla.blade.php";

        console.log(`Downloading ${remotePath}...`);
        await client.downloadTo(localPath, remotePath);

        const content = fs.readFileSync(localPath, 'utf8');
        if (content.includes("height: auto !important")) {
            console.log("✅ SUCCESS: Remote file contains the new CSS rules.");
        } else {
            console.log("❌ FAILURE: Remote file DOES NOT contain the new CSS rules.");
        }

        // Cleanup
        fs.unlinkSync(localPath);

    } catch (err) {
        console.log("Error:", err);
    }
    client.close();
}

verifyLayout();
