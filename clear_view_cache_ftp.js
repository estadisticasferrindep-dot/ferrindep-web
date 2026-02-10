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

async function clearViewCache() {
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

        const pathsToClear = [
            "/ferrindep/storage/framework/views",
            "/public_html/storage/framework/views"
        ];

        for (const viewsPath of pathsToClear) {
            console.log(`Listing files in ${viewsPath}...`);

            try {
                const list = await client.list(viewsPath);
                let deletedCount = 0;

                for (const file of list) {
                    if (file.name === "." || file.name === ".." || file.name === ".gitignore") continue;
                    if (file.isDirectory) continue;

                    try {
                        await client.remove(path.posix.join(viewsPath, file.name));
                        deletedCount++;
                    } catch (err) { }
                }
                console.log(`✅ Cleared ${deletedCount} cached view files from ${viewsPath}.`);
            } catch (err) {
                console.log(`⚠️ Could not access ${viewsPath}: ${err.message}`);
            }
        }

    } catch (err) {
        console.log("Error:", err);
    }
    client.close();
}

clearViewCache();
