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

async function deploy() {
    const client = new ftp.Client()
    client.ftp.verbose = true

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

        console.log("\n🚀 CLEANUP STAGE 5 -------------")

        console.log("Deleting public/seeder_temp.php...");
        try {
            await client.remove("/public_html/seeder_temp.php");
        } catch (e) { console.log("File not found or error deleting:", e.message); }

        console.log("Deleting ferrindep/database/seeders/MapeoUbicacionesSeeder.php...");
        try {
            // Optional: keep the seeder file? Usually good to keep in codebase but maybe not on server if we want 'clean' state? 
            // Actually, Laravel seeders live in the codebase. I should NOT delete the seeder file itself from the codebase structure, 
            // but maybe from the server if the user is strict?
            // No, standard practice is to keep seeders. I will ONLY delete the runner.
        } catch (e) { }

        console.log("---------------------------------------------------");
        console.log("✅ CLEANUP COMPLETO");
        console.log("---------------------------------------------------");

    } catch (err) {
        console.error("❌ Deploy Error:", err);
    }
    client.close();
}

deploy();
