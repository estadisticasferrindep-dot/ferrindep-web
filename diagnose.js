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

async function diagnose() {
    const client = new ftp.Client()
    try {
        await client.access({
            host: config.host,
            user: config.username,
            password: config.password,
            secure: false,
            port: config.port
        })

        console.log("--- LISTING / ---")
        const rootFiles = await client.list("/");
        console.log(rootFiles.map(f => (f.isDirectory ? "[D]" : "[F]") + " " + f.name).join("\n"));

        console.log("\n--- LISTING /public_html ---")
        try {
            const pubFiles = await client.list("/public_html");
            console.log(pubFiles.map(f => (f.isDirectory ? "[D]" : "[F]") + " " + f.name).join("\n"));
        } catch (e) { console.log("Error listing public_html: " + e.message); }

        console.log("\n--- LISTING /ferrindep ---")
        try {
            const ferrFiles = await client.list("/ferrindep");
            console.log(ferrFiles.map(f => (f.isDirectory ? "[D]" : "[F]") + " " + f.name).join("\n"));
        } catch (e) { console.log("Error listing ferrindep: " + e.message); }

    } catch (err) {
        console.log(err)
    }
    client.close()
}
diagnose()
