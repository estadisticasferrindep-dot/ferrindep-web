const fs = require('fs');
const history = require('./chat_history.json');

console.log(`Analyzing ${history.length} chats...`);

const keywords = ['precio', 'malla', 'envio', 'medida', 'tenes', 'stock', 'factura', 'rollo'];
const pairs = [];

history.forEach(chat => {
    const msgs = chat.messages;
    for (let i = 0; i < msgs.length - 1; i++) {
        const current = msgs[i];
        const next = msgs[i + 1];

        // Looking for USER -> BOT/OWNER flow
        if (current.from === 'USER' && next.from === 'BOT') {

            // Heuristic A: Question contains keyword
            const hasKeyword = keywords.some(k => current.body.toLowerCase().includes(k));

            // Heuristic B: Not too short, not too pretty (avoid "ok", "gracias")
            const decentLength = current.body.length > 10 && next.body.length > 10;

            if (hasKeyword && decentLength) {
                pairs.push({
                    q: current.body,
                    a: next.body,
                    source: chat.user
                });
            }
        }
    }
});

console.log(`Found ${pairs.length} potential Q&A pairs.`);
console.log("--- TOP 20 EXAMPLES ---");
pairs.slice(0, 20).forEach((p, i) => {
    console.log(`\n[${i + 1}] (${p.source})`);
    console.log(`Q: ${p.q}`);
    console.log(`A: ${p.a}`);
});
