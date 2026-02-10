const { GoogleGenerativeAI } = require("@google/generative-ai");

// USE THE SAME KEY AS BOT
const API_KEY = 'AIzaSyAVycAfefcHzy_YSrk0uxrbb9P4EXhC9nk';
const genAI = new GoogleGenerativeAI(API_KEY);

async function listModels() {
    try {
        const model = genAI.getGenerativeModel({ model: "gemini-pro" }); // Dummy init to get into SDK? 
        // Actually SDK doesn't have listModels on instance, usually separate or not in thin wrapper.
        // We can just try a simple fetch to the API endpoint manually if SDK fails, 
        // but SDK usually exposes it. 
        // Let's try to just run a generation with a known model to verify key first.

        console.log("Checking available models via direct API call...");
        // Node fetch or axios
        const axios = require('axios');
        const url = `https://generativelanguage.googleapis.com/v1beta/models?key=${API_KEY}`;

        const res = await axios.get(url);
        if (res.data && res.data.models) {
            console.log("✅ AVAILABLE MODELS:");
            res.data.models.forEach(m => {
                console.log(`- ${m.name} (${m.supportedGenerationMethods.join(', ')})`);
            });
        } else {
            console.log("❌ No models returned.", res.data);
        }

    } catch (e) {
        console.error("❌ Error listing models:", e.message);
        if (e.response) console.error(e.response.data);
    }
}

listModels();
