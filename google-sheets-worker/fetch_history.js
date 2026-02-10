const { Client, LocalAuth } = require('whatsapp-web.js');
const fs = require('fs');

const client = new Client({
    authStrategy: new LocalAuth({ clientId: 'client-two' }), // Matches bot_whatsapp.js
    puppeteer: {
        headless: true,
        args: ['--no-sandbox', '--disable-setuid-sandbox', '--disable-gpu']
    }
});

client.on('qr', (qr) => {
    console.log('⚠️ QR RECEIVED (Unexpected if reusing session)');
});

client.on('ready', async () => {
    console.log('✅ Client is ready! Fetching chats...');

    const chats = await client.getChats();
    console.log(`Found ${chats.length} chats.`);

    // Filter for non-group chats
    const nonGroup = chats.filter(c => !c.isGroup);

    // Prioritize "CTE" clients (Web Customers)
    const cteChats = nonGroup.filter(c => c.name && c.name.toUpperCase().startsWith('CTE'));
    const otherChats = nonGroup.filter(c => !c.name || !c.name.toUpperCase().startsWith('CTE'));

    console.log(`Found ${cteChats.length} 'CTE' chats (Web Customers).`);

    // Combine: All CTEs + recent others up to limit
    const limit = 150;
    const recentChats = [...cteChats, ...otherChats].slice(0, limit);

    const history = [];

    for (const chat of recentChats) {
        console.log(`Scanning: ${chat.name || chat.id.user}`);

        // Fetch last 20 messages
        const messages = await chat.fetchMessages({ limit: 20 });

        const chatLog = {
            user: chat.name || chat.id.user,
            phone: chat.id.user,
            messages: messages.map(m => ({
                from: m.fromMe ? 'BOT' : 'USER',
                body: m.body,
                timestamp: m.timestamp
            }))
        };

        history.push(chatLog);
    }

    fs.writeFileSync('chat_history.json', JSON.stringify(history, null, 2));
    console.log('💾 History saved to chat_history.json');
    console.log('Done. Exiting...');
    process.exit(0);
});

console.log('Initializing History Fetcher...');
client.initialize();
