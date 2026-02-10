const ftp = require('basic-ftp');
const fs = require('fs');

async function downloadRemote() {
    const client = new ftp.Client();
    try {
        const config = JSON.parse(fs.readFileSync('.vscode/sftp.json'));
        await client.access({
            host: config.host,
            user: config.username,
            password: config.password,
            secure: false
        });

        console.log("Downloading /public_html/resources/views/web/cliente/login.blade.php...");
        await client.downloadTo('login_remote.blade.php', '/public_html/resources/views/web/cliente/login.blade.php');

        console.log("Download complete.");

    } catch (err) {
        console.log(err);
    }
    client.close();
}

downloadRemote();
