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

        // DEFINIR LOS TARGETS (Donde vive el código)
        // Probamos ambos: ferrindep (Estructura correcta) y public_html (Por si acaso)
        // DEFINIR LOS TARGETS (Donde vive el código)
        // Probamos ambos: ferrindep (Estructura correcta) y public_html (Por si acaso)
        // IMPORTANTE: Rutas absolutas (con / al inicio)
        const targets = ["/ferrindep", "/public_html"];

        for (const base of targets) {
            console.log(`\n🚀 DESPLEGANDO EN: ${base} -------------------------`)

            try { await client.cd("/"); } catch (e) { } // Reset CWD

            // 1. Resources (Views, JS source, etc)
            console.log(`Subiendo resources/views a ${base}/resources/views...`);
            await client.ensureDir(`${base}/resources/views`);
            await client.uploadFromDir(path.join(localRoot, "resources/views"), `${base}/resources/views`);

            // 2. Routes (Web.php, Api.php)
            console.log(`Subiendo routes a ${base}/routes...`);
            await client.ensureDir(`${base}/routes`);
            await client.uploadFromDir(path.join(localRoot, "routes"), `${base}/routes`);

            // 3. App (Controllers, Models)
            console.log(`Subiendo app a ${base}/app...`);
            await client.ensureDir(`${base}/app`);
            await client.uploadFromDir(path.join(localRoot, "app"), `${base}/app`);

            // 4. Config (NUEVO: Para mercadolibre.php)
            console.log(`Subiendo config a ${base}/config...`);
            await client.ensureDir(`${base}/config`);
            await client.uploadFromDir(path.join(localRoot, "config"), `${base}/config`);
        }

        console.log("\n🚀 DESPLEGANDO ASSETS PÚBLICOS (/public_html) -------------")
        // 4. Public Assets (CSS, JS compilado)
        // Estos siempre van a public_html/css y public_html/js

        await client.ensureDir("/public_html/css");
        await client.uploadFromDir(path.join(localRoot, "public/css"), "/public_html/css");

        await client.ensureDir("/public_html/js");
        await client.uploadFromDir(path.join(localRoot, "public/js"), "/public_html/js");

        // 5. Archivos sueltos
        await client.uploadFrom("public/mix-manifest.json", "/public_html/mix-manifest.json");
        if (fs.existsSync("fix_bom.php")) await client.uploadFrom("fix_bom.php", "/public_html/fix_bom.php");
        if (fs.existsSync("fix_bom.php")) await client.uploadFrom("fix_bom.php", "/ferrindep/fix_bom.php");
        if (fs.existsSync("debug_info.php")) await client.uploadFrom("debug_info.php", "/public_html/debug_info.php");
        if (fs.existsSync("read_log.php")) await client.uploadFrom("read_log.php", "/public_html/read_log.php");
        if (fs.existsSync("public/update_env_remote.php")) await client.uploadFrom("public/update_env_remote.php", "/public_html/update_env_remote.php");
        if (fs.existsSync("public/setup_flex_system.php")) await client.uploadFrom("public/setup_flex_system.php", "/public_html/setup_flex_system.php");


        console.log("---------------------------------------------------");
        console.log("✅ FULL DEPLOY COMPLETE");
        console.log("---------------------------------------------------");

    } catch (err) {
        console.error("❌ Deploy Error:", err);
    }
    client.close();
}

deploy();
