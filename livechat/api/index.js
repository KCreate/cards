var ws_cfg = {
    ssl: true,
    port: 11231,
    ssl_key: '/etc/letsencrypt/archive/leonardschuetz.ch/privkey1.pem',
    ssl_cert: '/etc/letsencrypt/archive/leonardschuetz.ch/cert1.pem'
};

var https = require('https');
var fs = require('fs');
var app = null;
app = https.createServer({
    key: fs.readFileSync(ws_cfg.ssl_key),
    cert: fs.readFileSync(ws_cfg.ssl_cert)
}, function(req, res) {
    // Process request here
}).listen(ws_cfg.port);

var WebSocketServer = require('ws').Server, ws = new WebSocketServer({
    server: app
});

var chatrooms = {};
ws.on('connection', function(conn) { 
    
    console.log(chatrooms);

    conn.on('message', function(data) {

        // Convert to JSON
        data = JSON.parse(data);

        // Create the chatroom if it doesn't exist already
        if (!chatrooms.hasOwnProperty(data.chatroom)) {
            chatrooms[data.chatroom] = [];
        }

        console.log(chatrooms);


        // Check if the client is already registered
        var alreadyRegistered = chatrooms[data.chatroom].filter(function(client) {
            return (client.key == conn.upgradeReq.headers['sec-websocket-key']);
        });

        // Either register the player, or broadcast the new message
        if (alreadyRegistered.length == 0) {
            if (data.chatroom) {
                chatrooms[data.chatroom].push({
                    key: conn.upgradeReq.headers['sec-websocket-key']
                });
            }
        } else {

            // Don't save any messages, just delegate them
            if (data.message) {

                // Iterate over all active connections
                ws.clients.forEach(function(clientConnection) {

                    // Check if the current connection is in the right chatroom
                    var clientInChatroom = chatrooms[data.chatroom].filter(function(regClient) {
                        return (
                            regClient.key == clientConnection.upgradeReq.headers['sec-websocket-key']
                        );
                    });

                    // Check if the client is in the right chatroom
                    if (clientInChatroom.length != 0) {
                        
                        // Check if the clientConnection is open
                        if (clientConnection.readyState == 1) {
                            
                            // Send the message
                            clientConnection.send(JSON.stringify({
                                message: data.message,
                                users: chatrooms[data.chatroom].length,
                                chatroom: data.chatroom
                            }));
                        }
                    }
                });
            }
        }
    });

    conn.on('close', function() {
        Object.keys(chatrooms).forEach(function(e) {
            if (chatrooms.hasOwnProperty(e)) {
                for (var i=0; i<chatrooms[e].length; i++) {

                    if (chatrooms[e][i].key == conn.upgradeReq.headers['sec-websocket-key']) {
                        chatrooms[e].splice(i, 1);

                        // Remove the chatroom if it's empty
                        if (chatrooms[e].length == 0) { delete chatrooms[e]; }
                        break;
                    }
                }
            }
        });
    });
});
