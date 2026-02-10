const { JWT } = require('google-auth-library');
const creds = require('./credentials.json');
const axios = require('axios');

async function searchDriveFolder() {
    const serviceAccountAuth = new JWT({
        email: creds.client_email,
        key: creds.private_key,
        scopes: ['https://www.googleapis.com/auth/drive'],
    });

    try {
        const token = await serviceAccountAuth.getAccessToken();
        const headers = { Authorization: `Bearer ${token.token}` };

        // Search for folder
        const q = "mimeType = 'application/vnd.google-apps.folder' and name = 'ejercicios' and trashed = false";
        const url = `https://www.googleapis.com/drive/v3/files?q=${encodeURIComponent(q)}&fields=files(id, name)`;

        console.log("Searching for folder 'ejercicios'...");
        const res = await axios.get(url, { headers });

        if (res.data.files && res.data.files.length > 0) {
            console.log("Found folders:");
            for (const folder of res.data.files) {
                console.log(`- ${folder.name} (ID: ${folder.id})`);
                await listFolderContents(folder.id, headers);
            }
        } else {
            console.log("No folder named 'ejercicios' found accessible by this service account.");
            console.log("Note: Service accounts can only see files shared with them or created by them.");
        }

    } catch (e) {
        console.error("Error:", e.message);
        if (e.response) console.error(e.response.data);
    }
}

async function listFolderContents(folderId, headers) {
    const q = `'${folderId}' in parents and trashed = false`;
    const url = `https://www.googleapis.com/drive/v3/files?q=${encodeURIComponent(q)}&fields=files(id, name, mimeType)`;

    console.log(`Listing contents of folder ID ${folderId}...`);
    const res = await axios.get(url, { headers });

    if (res.data.files) {
        res.data.files.forEach(f => {
            console.log(`  - [${f.mimeType}] ${f.name} (ID: ${f.id})`);
        });
    }
}

searchDriveFolder();
