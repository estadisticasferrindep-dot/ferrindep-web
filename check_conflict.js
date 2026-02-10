const ftp = require('basic-ftp');
const fs = require('fs');

async function checkDirConflict() {
    const client = new ftp.Client();
    try {
        const config = JSON.parse(fs.readFileSync('.vscode/sftp.json'));
        await client.access({
            host: config.host,
            user: config.username,
            password: config.password,
            secure: false
        });

        console.log("Checking for /public_html/mis-compras directory...");
        const list = await client.list('/public_html');
        const collision = list.find(f => f.name === 'mis-compras');

        if (collision) {
            console.log("ALERT: Found 'mis-compras' item in root!");
            console.log(collision);
            if (collision.isDirectory) {
                console.log("It is a DIRECTORY. This explains why the route is ignored.");
            }
        } else {
            console.log("No 'mis-compras' directory found in public_html.");
        }

    } catch (err) {
        console.log(err);
    }
    client.close();
}

checkDirConflict();
