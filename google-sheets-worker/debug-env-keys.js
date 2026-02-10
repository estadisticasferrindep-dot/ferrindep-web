require('dotenv').config();

console.log("Environment Keys:");
Object.keys(process.env).forEach(key => {
    if (key.includes('SHEET') || key.includes('ID') || key.includes('TOKEN')) {
        console.log(key);
    }
});
