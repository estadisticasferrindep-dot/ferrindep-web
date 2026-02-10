const { GoogleGenerativeAI } = require("@google/generative-ai");

const API_KEY = 'AIzaSyC12jLF1IC6KmpSgK9UiCHSLHVBrn1RQVk';
const genAI = new GoogleGenerativeAI(API_KEY);

async function listModels() {
    console.log("🔍 Consultando modelos disponibles...");
    try {
        // For listing models, we don't need a specific model yet, just the permission
        // But the SDK exposes listModels usually via the module or a manager. 
        // Actually, in the widely used node SDK, we can usually just try to generate content to test connectivity,
        // or use the model manager if exposed. 
        // Let's try to just hit a simple prompt on 'gemini-1.5-flash' again but printing the full error object if it fails.

        // Attempt 1: Gemini 1.5 Flash
        console.log("\n--- Intento 1: gemini-1.5-flash ---");
        const model1 = genAI.getGenerativeModel({ model: "gemini-1.5-flash" });
        const result1 = await model1.generateContent("Hola");
        console.log("✅ Éxito! Respuesta:", result1.response.text());
        return;
    } catch (error) {
        console.log("❌ Falló 1.5 Flash.");
        // console.error(error);
    }

    try {
        // Attempt 2: Gemini 1.0 Pro
        console.log("\n--- Intento 2: gemini-1.0-pro ---");
        const model2 = genAI.getGenerativeModel({ model: "gemini-1.0-pro" });
        const result2 = await model2.generateContent("Hola");
        console.log("✅ Éxito! Respuesta:", result2.response.text());
        return;
    } catch (error) {
        console.log("❌ Falló 1.0 Pro.");
    }

    try {
        // Attempt 3: gemini-pro (Standard)
        console.log("\n--- Intento 3: gemini-pro ---");
        const model3 = genAI.getGenerativeModel({ model: "gemini-pro" });
        const result3 = await model3.generateContent("Hola");
        console.log("✅ Éxito! Respuesta:", result3.response.text());
        return;
    } catch (error) {
        console.log("❌ Falló gemini-pro.");
    }

}

listModels();
