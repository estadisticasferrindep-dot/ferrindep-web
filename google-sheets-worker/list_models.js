const { GoogleGenerativeAI } = require("@google/generative-ai");

// NEW Key (04/Feb/2026)
const API_KEY = 'AIzaSyAVycAfefcHzy_YSrk0uxrbb9P4EXhC9nk';
const genAI = new GoogleGenerativeAI(API_KEY);

async function listModels() {
    try {
        // There is no direct listModels method on the client instance in some versions,
        // but usually we can try to instantiate a model or check documentation.
        // Actually, the simpler way with the library is to just rely on trial and error OR 
        // use the REST API manually if the library doesn't expose listModels.
        // BUT, the error message from the previous turn SAID: "Call ListModels to see...".
        // The library might not expose it easily.
        // Let's try a direct fetch using node-fetch or axios since I have axios.

        const axios = require('axios');
        const url = `https://generativelanguage.googleapis.com/v1beta/models?key=${API_KEY}`;

        const response = await axios.get(url);
        console.log("AVAILABLE MODELS:");
        response.data.models.forEach(m => {
            console.log(`- ${m.name} (${m.displayName}) [Input Limit: ${m.inputTokenLimit}]`);
        });

    } catch (error) {
        console.error("Error listing models:", error.response ? error.response.data : error.message);
    }
}

listModels();
