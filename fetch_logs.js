const ftp = require('basic-ftp');
const fs = require('fs');

async function fetchLogs() {
    const client = new ftp.Client();
    try {
        const config = JSON.parse(fs.readFileSync('.vscode/sftp.json'));
        await client.access({
            host: config.host,
            user: config.username,
            password: config.password,
            secure: false
        });

        console.log("Downloading storage/logs/laravel.log...");
        // Download to a temp file
        await client.downloadTo('temp_laravel.log', '/ferrindep/storage/logs/laravel.log');

        console.log("Log downloaded to temp_laravel.log");

        // Read the last 200 lines to see recent errors or info
        const content = fs.readFileSync('temp_laravel.log', 'utf8');
        const lines = content.split('\n');
        const lastLines = lines.slice(-200).join('\n');

        console.log("--- LAST 200 LINES OF LOG ---");
        console.log(lastLines);
        console.log("-----------------------------");

    } catch (err) {
        console.log("Error fetching logs:", err);
    }
    client.close();
}

fetchLogs();
