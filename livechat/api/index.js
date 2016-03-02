var websocket = require('ws').Server;
var ws = websocket({port:11231});

var clients = [];
ws.on('connection', function(conn) {
    conn.on('message', function(data) {

        // Convert to JSON
        data = JSON.parse(data);
        console.log(conn.upgradeReq.headers);

        // Check if the client is already registered
        var alreadyRegistered = clients.filter(function(client) {
            return (client.key == conn.upgradeReq.headers['sec-websocket-key']);
        });

        // Either register the player, or broadcast the new message
        if (alreadyRegistered.length == 0) {
            if (data.chatroom) {
                clients.push({
                    chatroom: data.chatroom,
                    key: conn.upgradeReq.headers['sec-websocket-key']
                });
            }
        } else {

            // Don't save any messages, just delegate them
            if (data.message) {

                // Iterate over all active connections
                ws.clients.forEach(function(clientConnection) {

                    // Check if the current connection is in the right chatroom
                    var clientInChatroom = clients.filter(function(regClient) {
                        return (
                            regClient.chatroom == data.chatroom &&
                            regClient.key == clientConnection.upgradeReq.headers['sec-websocket-key']
                        );
                    });

                    // Check if the client is in the right chatroom
                    if (clientInChatroom.length != 0) {

                        // Send the message
                        clientConnection.send(JSON.stringify({
                            message: data.message
                        }));
                    }
                });
            }
        }
    });
});
