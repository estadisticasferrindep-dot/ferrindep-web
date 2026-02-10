const ftp = require('basic-ftp');
const fs = require('fs');

async function checkIndex() {
    const client = new ftp.Client();
    try {
        const config = JSON.parse(fs.readFileSync('.vscode/sftp.json'));
        await client.access({
            host: config.host,
            user: config.username,
            password: config.password,
            secure: false
        });

        console.log("Downloading /public_html/index.php...");
        await client.downloadTo('index_remote.php', '/public_html/index.php');

        console.log("Download complete.");

    } catch (err) {
        console.log(err);
    }
    client.close();
}

checkIndex();
