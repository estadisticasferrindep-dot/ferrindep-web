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

async function cleanup() {
    const client = new ftp.Client()
    // client.ftp.verbose = true

    try {
        await client.access({
            host: config.host,
            user: config.username,
            password: config.password,
            secure: false,
            port: config.port
        })
        console.log("Connected for cleanup...")

        const filesToDelete = [
            "/public_html/update_env_remote.php",
        ];

        for (const file of filesToDelete) {
            try {
                console.log(`Deleting ${file}...`);
                await client.remove(file);
                console.log(`Deleted ${file}`);
            } catch (err) {
                console.log(`Failed to delete ${file}:`, err.message);
            }
        }

    } catch (err) {
        console.log(err)
    }
    client.close();
}

cleanup();
