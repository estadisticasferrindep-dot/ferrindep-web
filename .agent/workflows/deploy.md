---
description: Deploy changes to production server via FTP
---
// turbo-all

After making any code changes, deploy automatically without asking the user.

1. If only Blade views changed (no JS/CSS compilation needed), deploy directly via FTP:
```
node -e "const ftp=require('basic-ftp'),fs=require('fs'),path=require('path');const c=JSON.parse(fs.readFileSync(path.join(__dirname,'.vscode','sftp.json'),'utf8'));(async()=>{const cl=new ftp.Client();try{await cl.access({host:c.host,user:c.username,password:c.password,secure:false,port:c.port});console.log('Connected!');const files=[LIST_OF_CHANGED_FILES];for(const f of files){await cl.uploadFrom(f,'/public_html/'+f);await cl.uploadFrom(f,'/ferrindep/'+f);console.log('Uploaded: '+f);}console.log('DEPLOY OK');}catch(e){console.error('Error:',e);}cl.close();})()"
```
Replace LIST_OF_CHANGED_FILES with the actual relative paths of modified files.

2. If JS/Vue components changed, first compile assets:
```
npm run prod
```
Then deploy compiled assets AND source files:
```
node deploy.js
```

3. If only controllers/routes/config changed (PHP files), deploy via FTP uploading just those files to both `/public_html` and `/ferrindep`.

4. Always deploy to BOTH `/public_html/` and `/ferrindep/` paths on the server.

5. FTP credentials are stored in `.vscode/sftp.json`.
