const ftp = require("basic-ftp")
const fs = require("fs")
const path = require("path")

const localRoot = __dirname;
let config = {};
try {
    config = JSON.parse(fs.readFileSync(path.join(localRoot, '.vscode', 'sftp.json'), 'utf8'));
} catch (e) {
    console.error("Error reading sftp.json", e);
    process.exit(1);
}

async function searchFile(client, dir, filename) {
    try {
        const list = await client.list(dir);
        for (const file of list) {
            if (file.name === "." || file.name === "..") continue;

            const fullPath = dir + (dir.endsWith("/") ? "" : "/") + file.name;

            if (file.name === filename) {
                console.log(`FOUND: ${fullPath} | Size: ${file.size} | Date: ${file.modifiedAt}`);
            }

            if (file.isDirectory && !file.name.startsWith(".")) {
                // Recursively search, but limit depth/scope to avoid hanging
                // Skip known big/irrelevant dirs
                if (['storage', 'vendor', 'node_modules', 'public', 'css', 'js', 'images', 'img'].includes(file.name)) continue;

                // console.log("Scanning " + fullPath);
                await searchFile(client, fullPath, filename);
            }
        }
    } catch (e) {
        // Ignore access errors
    }
}

async function run() {
    const client = new ftp.Client()
    // client.ftp.verbose = true

    try {
        console.log("Connecting...")
        await client.access({
            host: config.host,
            user: config.username,
            password: config.password,
            secure: false,
            port: config.port
        })
        console.log("Connected!")

        console.log("Searching for WebController.php in /ferrindep...");
        await searchFile(client, "/ferrindep", "WebController.php");

        console.log("Searching for WebController.php in /public_html...");
        await searchFile(client, "/public_html", "WebController.php");

    } catch (err) {
        console.error("Error:", err);
    }
    client.close();
}

run();
