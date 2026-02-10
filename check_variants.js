const ftp = require('basic-ftp');
const fs = require('fs');

async function checkLoginVariants() {
    const client = new ftp.Client();
    try {
        const config = JSON.parse(fs.readFileSync('.vscode/sftp.json'));
        await client.access({
            host: config.host,
            user: config.username,
            password: config.password,
            secure: false
        });

        console.log("Listing /public_html/resources/views/web/cliente/...");
        const list = await client.list('/public_html/resources/views/web/cliente/');

        console.log("Files found:");
        list.forEach(f => console.log(`- ${f.name} (${f.size})`));

        // Check Controller too
        console.log("Downloading Controller...");
        await client.downloadTo('MisComprasController_remote.php', '/public_html/app/Http/Controllers/MisComprasController.php');

    } catch (err) {
        console.log(err);
    }
    client.close();
}

checkLoginVariants();
