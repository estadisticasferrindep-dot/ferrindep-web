const ftp = require("basic-ftp");
const fs = require("fs");
const path = require("path");

// Load FTP configuration
let config;
try {
    const configPath = path.join(__dirname, ".vscode", "sftp.json");
    if (fs.existsSync(configPath)) {
        const configFile = fs.readFileSync(configPath, 'utf8');
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

async function checkStructure() {
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

        const targetPath = "/public_html/resources/views/layouts";
        console.log(`Listing files in ${targetPath}...`);

        try {
            const list = await client.list(targetPath);
            const found = list.find(f => f.name === "plantilla.blade.php");

            if (found) {
                console.log("✅ FOUND: plantilla.blade.php exists in /public_html/resources/views/layouts/");
                console.log(`Size: ${found.size}`);
            } else {
                console.log("❌ NOT FOUND: plantilla.blade.php is missing from the list.");
                console.log("Files found:");
                list.forEach(f => console.log(` - ${f.name}`));
            }
        } catch (err) {
            console.log(`❌ Error listing ${targetPath}:`, err.message);
            // Try parent
            const parentPath = "/public_html/resources/views";
            console.log(`Listing ${parentPath}...`);
            const parentList = await client.list(parentPath);
            parentList.forEach(f => console.log(` - ${f.name}`));
        }

    } catch (err) {
        console.log("Error:", err);
    }
    client.close();
}

checkStructure();
