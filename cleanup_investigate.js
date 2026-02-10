const ftp = require('basic-ftp');
const fs = require('fs');

async function deploy() {
    const client = new ftp.Client();
    try {
        const config = JSON.parse(fs.readFileSync('.vscode/sftp.json'));
        await client.access({
            host: config.host,
            user: config.username,
            password: config.password,
            secure: false
        });

        console.log("Removing investigate_zelaya_v2.php from public_html...");
        try {
            await client.remove('/public_html/investigate_zelaya_v2.php');
            console.log("Removed successfully.");
        } catch (e) {
            console.log("Error removing file: " + e.message);
        }

        console.log("Cleanup complete!");
    } catch (err) {
        console.log(err);
    }
    client.close();
}

deploy();
