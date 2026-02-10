const ftp = require('basic-ftp');
const fs = require('fs');

async function listRoot() {
    const client = new ftp.Client();
    try {
        const config = JSON.parse(fs.readFileSync('.vscode/sftp.json'));
        await client.access({
            host: config.host,
            user: config.username,
            password: config.password,
            secure: false
        });

        console.log("Listing root / ...");
        const list = await client.list('/');
        list.forEach(f => console.log(f.name + (f.isDirectory ? '/' : '')));

    } catch (err) {
        console.log(err);
    }
    client.close();
}

listRoot();
