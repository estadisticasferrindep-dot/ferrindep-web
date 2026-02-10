const ftp = require('basic-ftp');
const fs = require('fs');

async function clean() {
    const client = new ftp.Client();
    try {
        const config = JSON.parse(fs.readFileSync('.vscode/sftp.json'));
        await client.access({
            host: config.host,
            user: config.username,
            password: config.password,
            secure: false
        });

        console.log("Attempting to delete cached config...");
        try {
            await client.remove('/ferrindep/bootstrap/cache/config.php');
            console.log("Deleted bootstrap/cache/config.php");
        } catch (e) {
            console.log("Could not delete config.php (maybe it doesn't exist?): " + e.message);
        }

    } catch (err) { console.log(err); }
    client.close();
}
clean();
