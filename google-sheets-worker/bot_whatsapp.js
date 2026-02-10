const { Client, LocalAuth, MessageMedia } = require('whatsapp-web.js');
const { GoogleGenerativeAI } = require("@google/generative-ai");
const qrcodeFile = require('qrcode');
const fs = require('fs');
const axios = require('axios');
const { exec } = require('child_process');

// --- GEMINI CONFIG ---
// NEW CLEAN KEY (04/Feb/2026)
const API_KEY = 'AIzaSyAVycAfefcHzy_YSrk0uxrbb9P4EXhC9nk';
const genAI = new GoogleGenerativeAI(API_KEY);

// Use 'gemini-2.5-flash-preview-09-2025' (High Intelligence)
const model = genAI.getGenerativeModel({
    model: "gemini-2.5-flash-preview-09-2025",
    generationConfig: {
        maxOutputTokens: 8192,
        temperature: 0.3,
    }
});

// ... (Existing variables)

async function getGeminiResponse(userId, userMsg, imageData = null, filteredContext = null, audioData = null) {
    const now = new Date();
    const timeString = now.toLocaleTimeString('es-AR', { hour: '2-digit', minute: '2-digit' });

    try {
        // Initialize Session for this SPECIFIC User if not exists
        if (!chatSessions.has(userId)) {
            console.log(`✨ Creating new Gemini Session for ${userId}`);
            const session = model.startChat({
                history: [
                    { role: "user", parts: [{ text: `[SYSTEM: CURRENT TIME IS ${timeString}]\n` + BASE_SYSTEM_PROMPT }] },
                    { role: "model", parts: [{ text: "Entendido. Soy del equipo de Ferrindep, atiendo las consultas por WhatsApp." }] }
                ],
                safetySettings: [
                    { category: "HARM_CATEGORY_HARASSMENT", threshold: "BLOCK_NONE" },
                    { category: "HARM_CATEGORY_HATE_SPEECH", threshold: "BLOCK_NONE" },
                    { category: "HARM_CATEGORY_SEXUALLY_EXPLICIT", threshold: "BLOCK_NONE" },
                    { category: "HARM_CATEGORY_DANGEROUS_CONTENT", threshold: "BLOCK_NONE" }
                ]
            });
            chatSessions.set(userId, session);
        }

        const session = chatSessions.get(userId);

        // BUILD MESSAGE PARTS (Text + Optional Image/Audio)
        const messageParts = [];

        if (imageData) {
            // Add image as inline data for Gemini Vision
            messageParts.push({
                inlineData: {
                    mimeType: imageData.mimetype,
                    data: imageData.data // base64 string
                }
            });
            const caption = userMsg && userMsg.trim().length > 0
                ? userMsg
                : "El cliente envió esta imagen. Analizala y respondé como persona del equipo de Ferrindep. Si reconocés un producto o malla, indicá la medida aproximada y pasale el link de la web.";
            messageParts.push({ text: `[HORA: ${timeString}] ${caption}` });
            console.log(`📸 Sending image to Gemini for ${userId} (${imageData.mimetype})`);
        } else if (audioData) {
            // Add audio as inline data for Gemini
            messageParts.push({
                inlineData: {
                    mimeType: audioData.mimetype,
                    data: audioData.data // base64 string
                }
            });
            const audioPrompt = userMsg && userMsg.trim().length > 0
                ? `El cliente envió un audio junto con este texto: "${userMsg}". Escuchá el audio y respondé la consulta completa.`
                : "El cliente envió un audio de voz. Escuchá lo que dice y respondé su consulta como persona del equipo de Ferrindep. Si menciona un producto, buscalo en la base de conocimiento y pasale el link.";
            messageParts.push({ text: `[HORA: ${timeString}] ${audioPrompt}` });
            if (filteredContext) {
                messageParts.push({ text: filteredContext });
            }
            console.log(`🎤 Sending audio to Gemini for ${userId} (${audioData.mimetype}, ${Math.round(audioData.data.length * 0.75 / 1024)}KB)`);
        } else {
            let msgText = `[HORA: ${timeString}] User says: ${userMsg}`;
            if (filteredContext) {
                msgText += `\n${filteredContext}`;
            }
            messageParts.push({ text: msgText });
        }

        // TIMEOUT WRAPPER (60 Seconds for DEEP Thought)
        const timeoutPromise = new Promise((_, reject) => setTimeout(() => reject(new Error("Timeout Gemini API")), 60000));

        const result = await Promise.race([
            session.sendMessage(messageParts),
            timeoutPromise
        ]);

        const response = result.response;
        const candidate = response.candidates && response.candidates[0];
        const finishReason = candidate ? candidate.finishReason : 'UNKNOWN';

        // Log Safety or Blockage
        if (finishReason !== 'STOP') {
            console.warn(`⚠️ Gemini Finish Reason: ${finishReason}`);
            if (candidate && candidate.safetyRatings) {
                console.warn("🛡️ Safety Ratings:", JSON.stringify(candidate.safetyRatings, null, 2));
            }
        }

        let text = "";
        try {
            text = response.text();
        } catch (err) {
            console.error("❌ Error extracting text from response:", err.message);
        }

        console.log(`🤖 Gemini (for ${userId}) Length: ${text ? text.length : 0} | Reason: ${finishReason}`);

        if (!text || text.trim().length === 0) throw new Error("Empty Response");

        text = text.replace(/producto\/(\s|$|\.|,)/g, 'productos$1');

        return text;
    } catch (e) {
        console.error("⚠️ Gemini Error:", e.message);
        // Reset Session on Error to clear bad state for THIS user
        chatSessions.delete(userId);

        if (e.message.includes("Timeout")) {
            return "🐢 Estoy un poco lento hoy. Por favor preguntame de nuevo.";
        }
        return `⚠️ *Error de IA:* ${e.message}. Intenta de nuevo.`;
    }
}
async function fetchWebOrders(phone, text) {
    try {
        // Clean phone: Remove @c.us, +, spaces
        let cleanPhone = phone.replace('@c.us', '').replace(/\D/g, '');

        let url = `https://ferrindep.com.ar/api/bot/check-order?phone=${cleanPhone}`;

        // EXTRACT ORDER ID (e.g. "#3040", "3040" standalone, "pedido 3040", "compra 3040")
        // Check for standalone numeric ID or tagged with #/keyword
        const idMatch = text.match(/(?:#|pedido\s+|compra\s+)(\d{4,})/i) || text.match(/#?(\d{4,})/);
        let orderId = null;

        if (idMatch) {
            orderId = idMatch[1];
            console.log(`🔍 Buscando específicamente Pedido #${orderId}`);
            url += `&order_id=${orderId}`;
        }

        const res = await axios.get(url, { timeout: 5000 });

        if (res.data.success && res.data.orders.length > 0) {
            // RETURN RAW DATA FOR BOT TO DECIDE
            return {
                type: orderId ? 'ID' : 'PHONE',
                orders: res.data.orders
            };
        }
    } catch (e) {
        console.error("API Order Check Error:", e.message);
    }
    return null;
}

function formatOrderMessage(orders) {
    let msg = "📦 *Encontré estos pedidos en la web:*";
    orders.forEach(o => {
        msg += `\n\n🆔 *Pedido #${o.id}*\n📅 Fecha: ${o.fecha}\n💰 Total: $${o.total}\n🚦 Estado: *${o.estado}*\n🛒 Items: ${o.items}`;
        if (o.nombre) {
            msg += `\n👤 Titular: ${o.nombre}`;
        }
    });
    msg += "\n\n💡 *Tip:* Podés ver el detalle en tiempo real ingresando tu celular acá:\n👉 https://www.ferrindep.com.ar/mis-compras";
    return msg;
}
// --- ML DB REFRESHER ---
function checkAndRefreshMLData() {
    const ML_DB_FILE = './ml_products_db.json';
    const MAX_AGE_MS = 7 * 24 * 60 * 60 * 1000; // 7 Days

    let shouldUpdate = false;
    if (!fs.existsSync(ML_DB_FILE)) {
        console.log("⚠️ ML DB not found. Triggering initial fetch...");
        shouldUpdate = true;
    } else {
        const stats = fs.statSync(ML_DB_FILE);
        const age = Date.now() - stats.mtimeMs;
        if (age > MAX_AGE_MS) {
            console.log(`⚠️ ML DB is old (${(age / (1000 * 60 * 60 * 24)).toFixed(1)} days). Refreshing...`);
            shouldUpdate = true;
        }
    }

    if (shouldUpdate) {
        console.log("🔄 Running fetch_ml_links.js in background...");
        exec('node fetch_ml_links.js', (error, stdout, stderr) => {
            if (error) {
                console.error(`❌ Error updating ML DB: ${error.message}`);
                return;
            }
            if (stderr) console.error(`⚠️ Fetch Stderr: ${stderr.length > 500 ? stderr.substring(0, 500) + '...' : stderr}`);
            const safeStdout = stdout.length > 1000 ? stdout.substring(0, 1000) + '... (Output Truncated)' : stdout;
            console.log(`✅ ML DB Updated:\n${safeStdout}`);
            // Reload knowledge dynamically? Or just wait for next restart.
            // For simplicity, we just update the file for next time. 
            // Ideally we would reload 'messages' context, but restarting bot is safer/easier.
        });
    }
}

// --- LOAD KNOWLEDGE BASE ---
function loadKnowledge() {
    try {
        const prompt = fs.readFileSync('./cerebro_bot/system_prompt.md', 'utf8');
        const examples = fs.readFileSync('./cerebro_bot/examples.md', 'utf8');
        return prompt + "\n\n" + examples;
    } catch (e) {
        console.error("❌ Error loading Brain:", e.message);
        return "Actúa como un asistente profesional."; // Fallback
    }
}

// Load Web Products
const products = JSON.parse(fs.readFileSync('./products_db.json', 'utf8'));

// Load ML Products (Safe Mode)
let mlProducts = [];
try {
    if (fs.existsSync('./ml_products_db.json')) {
        mlProducts = JSON.parse(fs.readFileSync('./ml_products_db.json', 'utf8'));
    }
} catch (e) {
    console.error("⚠️ Could not load ML Products:", e.message);
}

// Load Web Variations (Scraped Heights)
let webVariations = [];
try {
    if (fs.existsSync('./web_variations_db.json')) {
        webVariations = JSON.parse(fs.readFileSync('./web_variations_db.json', 'utf8'));
    }
} catch (e) {
    console.error("⚠️ Could not load Web Variations:", e.message);
}

// Load Web Product Map (Grid+Height → Product URL)
let webProductMap = {};
try {
    if (fs.existsSync('./web_product_map.json')) {
        webProductMap = JSON.parse(fs.readFileSync('./web_product_map.json', 'utf8'));
        console.log(`✅ Web Product Map loaded: ${Object.keys(webProductMap).length} grids`);
    }
} catch (e) {
    console.error("⚠️ Could not load Web Product Map:", e.message);
}

// --- PRE-FILTER: DETECT GRID + HEIGHT from text ---
function detectProductSpecs(text) {
    const normalizedText = text.toLowerCase().replace(/,/g, '.').replace(/\s+/g, ' ');

    // 1. DETECT GRID SIZE (e.g. "15x15", "10 x 10", "50x50")
    const gridMatch = normalizedText.match(/(\d+)\s*[x×]\s*(\d+)/);
    if (!gridMatch) return null;

    const gridA = gridMatch[1];
    const gridB = gridMatch[2];

    // 2. DETECT HEIGHT - remove grid pattern first to avoid "15x15" matching "1.5"
    let heightCm = null;
    const textWithoutGrid = normalizedText.replace(/\d+\s*[x×]\s*\d+\s*mm/g, 'GRID');

    const heightMMatch = textWithoutGrid.match(/(\d+\.\d+)\s*(?:m(?:etros|ts?|t)?(?:\s|$|,|\.)|de alto)/);
    const heightCmMatch = textWithoutGrid.match(/(\d{2,3})\s*cm/);
    const heightSmallMatch = textWithoutGrid.match(/(0\.\d+)\s*m/);
    // Whole number meters: "1 metro", "2m", "1m de alto" (only match 1 or 2, not "29 metros de largo")
    const heightWholeMatch = textWithoutGrid.match(/(\d)\s*(?:m(?:etros?|ts?|t)?)\s*(?:de\s*alt|alto|\s|$|,)/);

    if (heightMMatch) {
        heightCm = Math.round(parseFloat(heightMMatch[1]) * 100);
    } else if (heightCmMatch) {
        heightCm = parseInt(heightCmMatch[1]);
    } else if (heightSmallMatch) {
        heightCm = Math.round(parseFloat(heightSmallMatch[1]) * 100);
    } else if (heightWholeMatch) {
        heightCm = parseInt(heightWholeMatch[1]) * 100;
    }

    // 3. DETECT PRODUCT TYPE
    const isMetalDesplegado = /desplegado/i.test(text);

    return { gridA, gridB, gridPattern: `${gridA}x${gridB}`, heightCm, isMetalDesplegado };
}

// --- PRE-FILTER WEB PRODUCT (Using web_product_map.json) ---
function preFilterWebProduct(specs) {
    if (!specs) return null;
    const { gridA, gridB, gridPattern, heightCm, isMetalDesplegado } = specs;

    // Try Metal Desplegado first if detected, then regular
    const gridKey = isMetalDesplegado ? `MD_${gridA}x${gridB}` : `${gridA}x${gridB}`;
    let gridEntry = webProductMap[gridKey] || webProductMap[`${gridKey}mm`];
    // Fallback: if Metal Desplegado not found, try regular and vice versa
    if (!gridEntry) {
        const altKey = isMetalDesplegado ? `${gridA}x${gridB}` : `MD_${gridA}x${gridB}`;
        gridEntry = webProductMap[altKey] || webProductMap[`${altKey}mm`];
    }

    if (!gridEntry) {
        console.log(`🌐 Web: No grid ${gridKey} in map`);
        return null;
    }

    if (heightCm) {
        const heightKey = `${heightCm}cm`;
        if (gridEntry[heightKey]) {
            console.log(`🌐 Web: Found ${gridKey} ${heightKey} → ${gridEntry[heightKey].url}`);
            return gridEntry[heightKey];
        }
        const nearHeights = Object.keys(gridEntry).sort((a, b) => {
            return Math.abs(parseInt(a) - heightCm) - Math.abs(parseInt(b) - heightCm);
        });
        if (nearHeights.length > 0) {
            console.log(`🌐 Web: No exact ${heightKey}, closest: ${nearHeights[0]} → ${gridEntry[nearHeights[0]].url}`);
            return gridEntry[nearHeights[0]];
        }
    }

    const firstHeight = Object.keys(gridEntry)[0];
    if (firstHeight) {
        console.log(`🌐 Web: No height, using ${firstHeight} → ${gridEntry[firstHeight].url}`);
        return gridEntry[firstHeight];
    }
    return null;
}

// --- PRE-FILTER ML PRODUCTS (Smart Search with Length Organization) ---
function preFilterMLProducts(text) {
    if (!mlProducts || mlProducts.length === 0) return null;

    const specs = detectProductSpecs(text);
    if (!specs) return null;

    const { gridA, gridB, gridPattern, heightCm, isMetalDesplegado } = specs;
    console.log(`🔍 Pre-filter: Detected grid ${gridPattern}mm${heightCm ? ', height ' + heightCm + 'cm' : ''}${isMetalDesplegado ? ' [METAL DESPLEGADO]' : ''}`);

    // 1. FILTER BY GRID SIZE + PRODUCT TYPE
    let filtered = mlProducts.filter(p => {
        const title = p.title.toLowerCase().replace(/,/g, '.');
        const gridMatch = title.includes(`${gridA}x${gridB}`) || title.includes(`${gridA} x ${gridB}`);
        if (!gridMatch) return false;
        // Separate metal desplegado from malla electrosoldada
        const isDesplegado = title.includes('desplegado');
        return isMetalDesplegado ? isDesplegado : !isDesplegado;
    });

    // 2. FILTER BY HEIGHT (if detected) - boundary-aware to avoid grid false matches
    if (heightCm && filtered.length > 0) {
        const hMeters = (heightCm / 100);

        const heightFiltered = filtered.filter(p => {
            const title = p.title.toLowerCase().replace(/,/g, '.');
            // Remove grid part to avoid "15x15" → "1.5" false match
            const titleNoGrid = title.replace(/\d+x\d+/g, 'GRID').replace(/\d+ x \d+/g, 'GRID');

            const heightPatterns = [
                new RegExp(`${heightCm}\\s*cm`, 'i'),
                new RegExp(`${hMeters.toFixed(2)}`, 'i'),
                new RegExp(`${hMeters.toFixed(1)}\\s*m`, 'i'),
                new RegExp(`${hMeters.toFixed(1)}\\s*x`, 'i'),
                new RegExp(`${hMeters}\\s*m`, 'i'),
            ];
            if (heightCm < 100) {
                heightPatterns.push(new RegExp(`0\\.${heightCm}`, 'i'));
                heightPatterns.push(new RegExp(`${heightCm}cm`, 'i'));
            }

            return heightPatterns.some(p => p.test(titleNoGrid));
        });

        if (heightFiltered.length > 0) {
            filtered = heightFiltered;
            console.log(`📏 Height filter: ${heightFiltered.length} products match ${heightCm}cm`);
        } else {
            console.log(`⚠️ Pre-filter: No exact height for ${heightCm}cm, showing all ${gridPattern}`);
        }
    }

    if (filtered.length === 0) return null;

    // 3. EXTRACT LENGTH FROM TITLES & ORGANIZE
    const withLength = filtered.map(p => {
        let lengthM = null;
        let category = 'otro';

        if (/x\s*metro\b/i.test(p.title) || /por\s*metro/i.test(p.title) || /alto\s*x\s*m(et|t)/i.test(p.title)) {
            category = 'por_metro';
            lengthM = 1;
        }
        else if (/alto\s*rollo/i.test(p.title) && !/\d+\s*m(ts|etros)?/i.test(p.title.replace(/\d+x\d+mm/gi, '').replace(/\d+[.,]\d+\s*m(t|ts)?\b/gi, ''))) {
            category = 'rollo_completo';
            lengthM = 20;
        }
        else {
            let cleanTitle = p.title.replace(/\d+x\d+mm/gi, '').replace(/\d+[.,]\d+\s*m(t|ts)?\b/gi, '');
            const lenMatch = cleanTitle.match(/(\d+(?:[.,]\d+)?)\s*m(?:ts|etros|t)?\b/i);
            if (lenMatch) {
                lengthM = parseFloat(lenMatch[1].replace(',', '.'));
                category = lengthM <= 1 ? 'por_metro' : (lengthM <= 5 ? 'rollo_corto' : 'rollo_largo');
            }
        }

        return { ...p, lengthM, category };
    });

    // Remove exact duplicates
    const seen = new Set();
    const unique = withLength.filter(p => {
        const key = p.title.trim();
        if (seen.has(key)) return false;
        seen.add(key);
        return true;
    });

    // Sort: por_metro first, then by length
    unique.sort((a, b) => {
        const catOrder = { por_metro: 0, rollo_corto: 1, rollo_largo: 2, rollo_completo: 3, otro: 4 };
        const ca = catOrder[a.category] ?? 4;
        const cb = catOrder[b.category] ?? 4;
        if (ca !== cb) return ca - cb;
        return (a.lengthM || 99) - (b.lengthM || 99);
    });

    console.log(`✅ Pre-filter: Found ${unique.length} unique ML products for ${gridPattern}mm${heightCm ? ' ' + heightCm + 'cm' : ''}`);

    // 4. FORMAT FOR GEMINI
    const formatItem = (p) => {
        const lenStr = p.category === 'por_metro' ? '[POR METRO]' :
            p.category === 'rollo_completo' ? '[ROLLO COMPLETO ~20m]' :
                p.lengthM ? `[${p.lengthM}m]` : '';
        return `- ${lenStr} ${p.title} (${p.permalink})`;
    };

    const lines = unique.map(formatItem).join('\n');

    // Also get web product link
    const webProduct = preFilterWebProduct(specs);
    let webLine = '';
    if (webProduct) {
        webLine = `\n[LINK WEB CORRECTO PARA ESTA CONSULTA]: ${webProduct.name} => ${webProduct.url}\nUSÁ ESTE LINK WEB, NO OTRO. Es el producto correcto para esta abertura y altura.\n`;
    }

    return `${webLine}\n[PRODUCTOS ML FILTRADOS - ${gridPattern}mm${heightCm ? ' altura ' + heightCm + 'cm' : ''}]:\n${lines}\n[FIN FILTRO - Los items [ROLLO COMPLETO ~20m] son rollos enteros. Ofrecé AL MENOS 2 opciones de largo diferente. NUNCA menciones precios.]`;
}


// Use Web Variations if available, otherwise fallback to basic products
const webOpt = webVariations.length > 0
    ? webVariations.map(p => `${p.full_spec} (${p.url})`).join('\n')
    : products.map(p => `${p.title} (${p.link})`).join('\n');

// ML summary for base prompt (no full list - too many tokens)
// The pre-filter will inject the relevant products dynamically per message
const mlGridSizes = [...new Set(mlProducts.map(p => {
    const m = p.title.match(/(\d+x\d+)/i);
    return m ? m[1] + 'mm' : null;
}).filter(Boolean))].sort();
const mlSummary = `Tenemos ${mlProducts.length} publicaciones en MercadoLibre. Aberturas disponibles: ${mlGridSizes.join(', ')}. Los links exactos se inyectan automáticamente según la consulta del cliente.`;

// Combine for Brain
const BASE_SYSTEM_PROMPT = loadKnowledge() +
    `\n\nBASE DE CONOCIMIENTO (WEB: ferrindep.com.ar - CON MEDIDAS EXACTAS):\n${webOpt}` +
    `\n\nMERCADOLIBRE (INFO GENERAL):\n${mlSummary}\nNOTA: Los links de ML relevantes se adjuntan automáticamente a cada consulta. Cuando veas [PRODUCTOS ML FILTRADOS], usá esos links para responder.` +

    `\n\nREGLAS DE VENTA (IMPORTANTE):\n` +
    `1. SI EL CLIENTE DICE "10x10", ASUME QUE ES "10x10 mm".\n` +
    `2. SI FALTA UN DATO (ej: Altura), PREGUNTÁ: "¿De qué altura busca? Tenemos de 30cm, 40cm, 50cm, 60cm, etc." (Fijate en la lista WEB qué alturas existen).\n` +
    `3. SI ENCONTRÁS LA MEDIDA EXACTA (ej: 40cm), CONFIRMALE: "Sí, tenemos de 40cm de alto. Le dejo el enlace de la web para que vea el precio y pueda comprar."\n` +
    `4. SIEMPRE pasá los links de compra (Web y ML) para cerrar la venta.\n` +
    `5. RECORDÁ: Los rollos suelen venir de 10 metros de largo (o 25m en algunos casos). Acláralo si preguntan.\n` +
    `6. ESTRUCTURA DE RESPUESTA: "Sí, tenemos malla de 10x10mm en 40cm de alto. Le dejo el enlace: [LINK]".\n` +
    `7. REGLA DE DESCONOCIMIENTO / FUERA DE CONTEXTO (CLAVE):\n` +
    `   - Si preguntan por envíos de MercadoLibre, reclamos, o algo que NO ESTÁ en tu base de datos o en los links de la web:\n` +
    `   - NO INVENTES. NO DIGAS "SARASA".\n` +
    `   - RESPONDÉ EXACTAMENTE ESTO: "No tengo esa información por el momento. En breve se contactará una persona para asistirle mejor."\n` +
    `   (Esto avisará automáticamente a Mauro para que intervenga).\n\n` +
    `REGLA DE PRECIOS (ABSOLUTAMENTE PROHIBIDO):\n` +
    `- NUNCA menciones precios de MercadoLibre en el chat. NO DIGAS "$82.376" ni ningún monto.\n` +
    `- Si el cliente pregunta "¿cuánto sale?", redirigilo a la web: "Le dejo el enlace de la web donde puede ver el precio actualizado y el stock en tiempo real."\n` +
    `- Los precios cambian constantemente. SIEMPRE derivar a la web o al link de ML para que vea el precio ahí.\n` +
    `- Esto aplica tanto para precios de la web como para precios de ML. NO CITAR MONTOS.\n\n` +
    `DATOS TÉCNICOS CRÍTICOS (MALLAS):\n` +
    `- ESPESOR MÁXIMO (ROLLOS GALVANIZADOS): 3.3 mm (Nosotros NO comercializamos mayor espesor en rollo. Si piden 4mm, invitalos a la web a ver lo que sí tenemos).\n` +
    `  * Medidas disponibles: 8x17mm (Espesor 1mm), 18x40mm (Espesor 2mm), 20x50mm (Espesor 2mm).\n` +
    `  * NO comercializamos de 4mm de espesor en rollo. Invitalos generosamente a ver los modelos disponibles en la web.\n\n` +
    `GUÍA DE USOS Y RECOMENDACIONES (SUGERENCIAS DE EXPERTO):\n` +
    `- CERCOS DE CALLE (PERIMETRAL DESEGURIDAD): Recomendamos 50x150mm 3.3mm (Panel o Rollo pesado). Es lo más seguro.\n` +
    `- DEBAJO DE ALAMBRADOS (ANTI-EXCAVACIÓN / COIPOS / PERROS): Ideal 25x25mm (1.5mm) o 50x50mm. Alturas típicas: 50cm o 60cm (aunque algunos prefieren 1m). El espesor 0.9mm es muy fino para esto.\n` +
    `- GUÍA DE PLANTAS / TREPADORAS: Usan mucho 50x50mm en 1.6mm o 1.9mm.\n` +
    `- GENERAL (JAULAS, CERRAMIENTOS, REJAS): Espesores finos (0.9mm a 1.5mm) son más maleables y fáciles de trabajar.\n\n` +
    `REGLAS DE NEGOCIO (ESTRICTAS):\n` +
    `1. CORTES Y FRACCIONAMIENTO: SÍ vendemos por metro (ej: 1m, 2m, 3m, 4m, 10m). Algunos productos permiten 2.5m o 3.5m.\n` +
    `   - REGLA DE ORO: GUÍATE POR LAS OPCIONES DE LA WEB. Si en la web sale "1 Metro", "2 Metros", entonces SÍ se puede.\n` +
    `   - NO CORTAMOS MEDIDAS ARBITRARIAS (Ej: 12.35m, 13.7m). En esos casos, SUGERÍ amablemente opciones válidas cercanas: "No podemos cortar 12.35m exactos, pero puede llevar 12m o 13m."\n` +
    `2. COTIZACIONES FORMALES / PROFORMAS / MAYORISTAS: Si piden "Presupuesto formal", "Factura A con detalle", o "Cotización PDF":\n` +
    `   - RESPONDÉ: "Para cotizaciones formales o pedidos especiales, por favor aguarde que un vendedor se contactará en breve. MIENTRAS TANTO, SI PUEDE, DÉJEME EL DETALLE EXACTO (Producto, Cantidad, CUIT y Zona de Entrega) así agilizamos."\n` +
    `   - ESTO DEBE ACTIVAR EL CAMBIO A HUMANO (La frase "contactará una persona").\n` +
    `EJEMPLO DE INTERACCIÓN PERFECTA (CLAVE PARA VENDER):\n` +
    `- Cliente: "Precio de 10x10?"\n` +
    `- Bot: "¿De qué altura busca? Tenemos 0.50m, 1.00m..."\n` +
    `- Cliente: "De 1 metro"\n` +
    `- Bot: "Perfecto. Le dejo el enlace de la web donde puede ver el precio actualizado y comprar:\n[LINK WEB]\nTambién lo tiene en MercadoLibre por metro o en rollo:\n[LINK ML 1m]\n[LINK ML 10m]"\n` +
    `SI NO ESTÁS 100% SEGURO DEL LINK EXACTO, MÁNDALO IGUAL PERO CON ESTA ADVERTENCIA:\n` +
    `"Le dejo este enlace, por favor verifique que sea la medida correcta antes de comprar: [LINK_POSIBLE]"\n` +
    `LO IMPORTANTE ES QUE ENTREN A LA WEB. ANTE LA DUDA, MANDÁ EL LINK GENERAL.\n\n` +
    `ORDEN DE PRIORIDAD DE LINKS (SAGRADO):\n` +
    `1. PRIMERO: Link de la WEB (ferrindep.com.ar). Es lo más importante.\n` +
    `2. SEGUNDO: Links de MercadoLibre (como alternativa). SIEMPRE intentá ofrecer AL MENOS 2 opciones de ML si existen (ej: rollo de 1m y rollo de 10m, o rollo de 5m y 10m). Buscá en la Base ML otros largos del MISMO producto.\n` +
    `3. SOLICITUD DE IMÁGENES / FOTOS: Si piden "Mandame una foto", "Quiero ver una imagen", etc.:\n` +
    `   - RESPONDÉ TEXTUALMENTE: "Por el momento no tengo fotos a mano para mandarle por acá, pero puede ver todo el contenido en nuestra web: www.ferrindep.com.ar"\n` +
    `   - NO INVENTES EXCUSAS RARAS NI REVELES QUE SOS UN BOT O IA. Usá la frase oficial.\n` +
    `8. FILOSOFÍA DE RESPUESTA (MODO "PENSADOR"): Tómate tu tiempo para entender bien la consulta. TU OBJETIVO FINAL NO ES SOLO DAR INFO, SINO LOGRAR QUE EL CLIENTE ENTRE A LA WEB (FERRINDEP.COM.AR).\n` +
    `   - SIEMPRE decí algo como: "En la web puede ver el precio actualizado y el stock en tiempo real".\n` +
    `   - SIEMPRE tratá de "educar" al cliente para que use la web.`;

console.log(`🧠 Cerebro cargado: ${products.length} Items Web + ${mlProducts.length} Items ML.`);

// TRIGGER ML CHECK
checkAndRefreshMLData();

// --- WHATSAPP CONFIG ---
const client = new Client({
    authStrategy: new LocalAuth({ clientId: 'client-two' }),
    puppeteer: {
        headless: true,
        args: ['--no-sandbox', '--disable-setuid-sandbox', '--disable-gpu']
    }
});

// Removed global chatSession
const chatSessions = new Map(); // Store sessions per user: userId -> session
const manualInterventions = new Map(); // Store silence expiry: userId -> timestamp
const verificationSessions = new Map(); // Verificacion state for order lookups
const botTyping = new Set(); // Track active bot replies to prevent self-silencing
const processedMessages = new Set(); // Track already-processed message IDs (anti-duplicate)
const processingUsers = new Set(); // Lock: prevent concurrent processing for the same user

// --- MESSAGE BUFFER (Accumulate rapid-fire messages) ---
const messageBuffers = new Map(); // userId -> { messages: [], timer: null, imageData: null }
const BUFFER_WAIT_MS = 4000; // Wait 4 seconds for more messages before processing

// Cleanup old message IDs every 10 minutes to prevent memory leak
setInterval(() => {
    processedMessages.clear();
    console.log("🧹 Cleared processedMessages cache.");
}, 10 * 60 * 1000);

async function replyAsBot(message, text, options = {}) {
    const chatId = message.from; // The user we are replying to
    botTyping.add(chatId);
    try {
        const response = await client.sendMessage(chatId, text, options);
        // Keep the flag active for a short bit to cover the event emission delay
        setTimeout(() => botTyping.delete(chatId), 5000);
        return response;
    } catch (e) {
        botTyping.delete(chatId);
        throw e;
    }
}

client.on('qr', (qr) => {
    console.log('QR Code received. Saving to qr_code.png...');
    qrcodeFile.toFile('./qr_code.png', qr, {
        color: { dark: '#000000', light: '#ffffff' }
    }, function (err) {
        if (err) throw err;
        console.log('✅ QR Image saved! Open "qr_code.png" to scan.');
    });
});

client.on('ready', () => {
    console.log('Client is ready! 🟢 (AI Active)');
});

// --- SMART SILENCE: DETECT HUMAN INTERVENTION ---
client.on('message_create', async (msg) => {
    // If I sent the message (fromMe) and it's NOT the bot (how to distinguish? usually via prefix or context, but here we assume any manual msg)
    // Actually, 'message_create' fires for ALL messages, including Bot's.
    // We need to distinguish Bot messages vs Human messages. 
    // The Bot sends via client.sendMessage which triggers this.
    // However, if we assume the human uses WhatsApp Web/Mobile-App, those also come here as fromMe=true.

    if (msg.fromMe) {
        // Allow Bot to reply without silencing itself
        // The replyAsBot function adds the chatId to botTyping set
        if (botTyping.has(msg.to)) {
            // It's the bot replying
            return;
        }

        // It's a MANUAL message from the Human (Mauro)
        const chatHeader = msg.to;
        console.log(`🙊 HUMAN INTERVENTION DETECTED in ${chatHeader}. Silencing Bot for 24 hours.`);
        // Set 24 Hours Silence
        manualInterventions.set(chatHeader, Date.now() + (24 * 60 * 60 * 1000));
    }
});

// --- 1. SET STARTUP TIME TO IGNORE OLD MESSAGES ---
const STARTUP_TIME = Math.floor(Date.now() / 1000);
let isPaused = false; // Estado Global del Bot

// --- PROCESS BUFFERED MESSAGES (Core Logic) ---
async function processBufferedMessages(userId) {
    const buffer = messageBuffers.get(userId);
    if (!buffer || buffer.messages.length === 0) {
        messageBuffers.delete(userId);
        return;
    }

    // Extract all data and clear buffer
    const messages = [...buffer.messages];
    const imageData = buffer.imageData;
    const audioData = buffer.audioData;
    const lastMessage = messages[messages.length - 1]; // Use last message for reply context
    messageBuffers.delete(userId);

    // Set processing lock
    processingUsers.add(userId);

    try {
        const OWNER_NUMBER = '5491132631520@c.us';
        const chat = await lastMessage.getChat();
        const contactName = chat.name || "Unknown";

        // --- 1. HANDLE VERIFICATION FLOW (Priority) ---
        if (verificationSessions.has(userId)) {
            const session = verificationSessions.get(userId);
            const userText = messages.map(m => m.body).join(' ').toLowerCase().trim();
            const targetName = session.targetName.toLowerCase();

            const parts = targetName.split(' ');
            const isMatch = parts.some(part => part.length > 3 && userText.includes(part));

            if (isMatch) {
                console.log(`✅ Verificación Exitosa: ${userText} matches ${targetName}`);
                await replyAsBot(lastMessage, "✅ *Identidad Confirmada.*");
                const finalMsg = formatOrderMessage(session.orders);
                await replyAsBot(lastMessage, finalMsg);
                verificationSessions.delete(userId);
            } else {
                console.log(`❌ Fallo Verificación: ${userText} vs ${targetName}`);
                session.attempts++;
                if (session.attempts >= 2) {
                    await replyAsBot(lastMessage, "❌ No pude verificar la identidad. Por seguridad, no puedo mostrar el detalle. Contactate con un vendedor.");
                    verificationSessions.delete(userId);
                } else {
                    await replyAsBot(lastMessage, "⚠️ Ese nombre no coincide con el del pedido. Por favor intentá de nuevo (Ej: Nombre o Apellido del titular).");
                }
            }
            chat.clearState();
            return;
        }

        // Combine all text messages into one
        const combinedText = messages.map(m => m.body || "").filter(t => t.trim()).join('\n');
        console.log(`📦 Processing ${messages.length} buffered message(s) from ${userId}: "${combinedText.substring(0, 100)}..."`);

        // --- 2. ORDER CHECK INTENT (check combined text) ---
        if (/pedido|compra|estado|status|tracking|donde|demora|#\d+|^\d{4,}$/im.test(combinedText.trim())) {
            console.log("🔍 Intent: Pedidos Web...");
            const orderData = await fetchWebOrders(userId, combinedText);

            if (orderData) {
                if (orderData.type === 'ID') {
                    const targetOrder = orderData.orders[0];
                    const targetName = targetOrder.nombre || "Cliente";

                    console.log(`🔒 Iniciando verificación para ID #${targetOrder.id} (Titular: ${targetName})`);

                    verificationSessions.set(userId, {
                        targetName: targetName,
                        orders: orderData.orders,
                        attempts: 0
                    });

                    await replyAsBot(lastMessage, `🔒 Encontré el pedido *#${targetOrder.id}*. \n\nPor motivos de seguridad, decime: *¿A nombre de quién está el pedido?*`);
                    chat.clearState();
                    return;
                } else {
                    const msg = formatOrderMessage(orderData.orders);
                    await replyAsBot(lastMessage, msg);
                    chat.clearState();
                    return;
                }
            }
        }

        // --- 3. AI GEMINI (With Vision Support + ML Pre-Filter) ---
        const filteredML = preFilterMLProducts(combinedText);
        if (filteredML) {
            console.log(`🧠 Injecting filtered ML context into Gemini message`);
        }
        const response = await getGeminiResponse(userId, combinedText, imageData, filteredML, audioData);

        // NATURAL DELAY (Human Behavior Modeling)
        await new Promise(resolve => setTimeout(resolve, 5000));
        await chat.sendStateTyping();
        await new Promise(resolve => setTimeout(resolve, 3000));

        await chat.clearState();
        await replyAsBot(lastMessage, response);

        // --- AUTO-SEND PRODUCT IMAGE (Scraped from Product Page) ---
        try {
            const productUrlMatch = response.match(/ferrindep\.com\.ar\/productos\/producto\/(\d+)/);
            if (productUrlMatch) {
                const productId = productUrlMatch[1];
                const productPageUrl = `https://www.ferrindep.com.ar/productos/producto/${productId}`;
                console.log(`📸 Detected product ID ${productId}, scraping image from page...`);

                // Fetch the product page HTML
                const pageResponse = await axios.get(productPageUrl, { timeout: 10000 });
                const html = pageResponse.data;

                // Extract first product image (from storage/imagenes/ path)
                const imgRegex = /<img[^>]+src=["'](https?:\/\/[^"']*storage\/imagenes\/[^"']+)["'][^>]*>/i;
                const imgMatch = html.match(imgRegex);

                if (imgMatch && imgMatch[1]) {
                    const imageUrl = imgMatch[1];
                    console.log(`🖼️ Found product image: ${imageUrl.substring(0, 80)}...`);

                    // Download image directly
                    const imgDownload = await axios.get(imageUrl, { responseType: 'arraybuffer', timeout: 15000 });
                    const imgBase64 = Buffer.from(imgDownload.data).toString('base64');
                    const imgMime = imgDownload.headers['content-type'] || 'image/jpeg';
                    const media = new MessageMedia(imgMime, imgBase64, `product_${productId}.jpg`);
                    await client.sendMessage(userId, media);
                    console.log(`✅ Product image sent for ID ${productId}`);
                } else {
                    console.log(`⚠️ No product image found in page HTML for ID ${productId}`);
                }
            }
        } catch (imgErr) {
            console.error("⚠️ Error sending product image:", imgErr.message);
        }

        // Notify Handoff
        if (response.includes("contactará una persona")) {
            const chatLink = `https://wa.me/${userId.split('@')[0]}`;
            client.sendMessage(OWNER_NUMBER, `⚠️ *ALERTA:* Humano requerido.\n👤 ${contactName}\n📱 ${chatLink}`).catch(() => { });
        }

    } catch (err) {
        console.error("Processing Error:", err);
    } finally {
        processingUsers.delete(userId);
    }
}

// --- MAIN MESSAGE HANDLER (Buffer + Process) ---
client.on('message', async message => {
    try {
        // --- ANTI-DUPLICATE: Skip if this exact message was already processed ---
        const msgId = message.id && message.id._serialized ? message.id._serialized : message.id;
        if (processedMessages.has(msgId)) return;
        processedMessages.add(msgId);

        const OWNER_NUMBER = '5491132631520@c.us';

        // --- ADMIN COMMANDS (Priority 0 - IMMEDIATE, no buffer) ---
        if (message.from === OWNER_NUMBER) {
            if (message.body.trim().toLowerCase() === '!pausa') {
                isPaused = true;
                await replyAsBot(message, "💤 Bot Puesto en PAUSA. No responderé a nadie hasta que me despiertes con !activar");
                console.log("⏸️ BOT PAUSED BY OWNER");
                return;
            }
            if (message.body.trim().toLowerCase() === '!activar') {
                isPaused = false;
                await replyAsBot(message, "🟢 Bot ACTIVADO. Volví al trabajo.");
                console.log("▶️ BOT RESUMED BY OWNER");
                return;
            }
            if (message.body.trim() === '!apagar') {
                await replyAsBot(message, "🔌 Apagando proceso...");
                setTimeout(() => process.exit(0), 1000);
                return;
            }
        }

        // --- PAUSE CHECK ---
        if (isPaused) return;

        // --- SMART SILENCE CHECK ---
        if (manualInterventions.has(message.from)) {
            const expiry = manualInterventions.get(message.from);
            if (Date.now() < expiry) {
                console.log(`😶 Silenced for ${message.from} (Human took over)`);
                return;
            } else {
                manualInterventions.delete(message.from);
            }
        }

        // --- IGNORE OLD MESSAGES ---
        if (message.timestamp <= STARTUP_TIME) return;

        // --- FILTERS ---
        if (message.from.endsWith('@g.us')) return;
        if (message.from === 'status@broadcast' || message.from.includes('@newsletter')) return;
        if (message.fromMe) return;

        // --- INTERNAL CONTACT FILTER ---
        const chat = await message.getChat();
        const contactName = chat.name || "Unknown";
        const IGNORE_LIST = ['DEPO', 'LOGISTICA', 'PAPA', 'MAMA', 'CONTADOR', 'BRIO', 'TONY', 'GRA', 'UMI', 'RI'];
        if (new RegExp(`\\b(${IGNORE_LIST.join('|')}) \\b`, 'i').test(contactName)) {
            console.log(`🚫 Ignorando interno: ${contactName}`);
            return;
        }

        // --- IF BOT IS ALREADY PROCESSING FOR THIS USER, BUFFER ANYWAY ---
        // (The buffer will be picked up after current processing finishes)

        // --- HANDLE MEDIA IMMEDIATELY (video rejection, image/audio buffering) ---
        if (message.hasMedia) {
            try {
                const media = await message.downloadMedia();
                if (media && media.mimetype && media.mimetype.startsWith('video/')) {
                    await replyAsBot(message, "Por el momento no puedo ver videos por acá, pero si me manda una foto o me describe lo que busca, lo ayudo enseguida.");
                    return;
                } else if (media && media.mimetype && (media.mimetype.startsWith('audio/') || media.mimetype.includes('ogg'))) {
                    // Buffer audio for Gemini processing (voice messages)
                    if (!messageBuffers.has(message.from)) {
                        messageBuffers.set(message.from, { messages: [], timer: null, imageData: null, audioData: null });
                    }
                    messageBuffers.get(message.from).audioData = { mimetype: media.mimetype, data: media.data };
                    console.log(`🎤 Audio buffered from ${message.from} (${media.mimetype}, ${Math.round(media.data.length * 0.75 / 1024)}KB)`);
                } else if (media && media.mimetype && media.mimetype.startsWith('image/')) {
                    // Store image in buffer
                    if (!messageBuffers.has(message.from)) {
                        messageBuffers.set(message.from, { messages: [], timer: null, imageData: null, audioData: null });
                    }
                    messageBuffers.get(message.from).imageData = { mimetype: media.mimetype, data: media.data };
                    console.log(`📸 Image buffered from ${message.from} (${media.mimetype}, ${Math.round(media.data.length * 0.75 / 1024)}KB)`);
                }
            } catch (mediaErr) {
                console.error("⚠️ Error downloading media:", mediaErr.message);
            }
        }

        // --- BUFFER THE MESSAGE ---
        if (!messageBuffers.has(message.from)) {
            messageBuffers.set(message.from, { messages: [], timer: null, imageData: null, audioData: null });
        }
        const buffer = messageBuffers.get(message.from);
        buffer.messages.push(message);

        // Reset the debounce timer (wait for more messages)
        if (buffer.timer) clearTimeout(buffer.timer);

        const userId = message.from;
        buffer.timer = setTimeout(async () => {
            // Don't process if bot is already processing for this user
            // Wait for current processing to finish first
            while (processingUsers.has(userId)) {
                await new Promise(resolve => setTimeout(resolve, 500));
            }
            processBufferedMessages(userId);
        }, BUFFER_WAIT_MS);

        console.log(`📥 Message buffered for ${message.from} (${buffer.messages.length} in buffer, waiting ${BUFFER_WAIT_MS}ms...)`);

    } catch (outerErr) {
        console.error("Critical Error:", outerErr);
    }
});
client.initialize();

// Keep Process Alive
setInterval(() => { }, 1000 * 60 * 60); // 1 hour loop
