<?php
require_once('api.php');
?>
<!DOCTYPE html>
<html>
    <head>
        <?php
        include_once $SM->get_def("presets", "head", 2);
        ?>
    </head>
    <body>
        <div id="hero">
            <div>
                <h1>Livechat</h1>
            </div>
            <ul>
                <li><a id="index" href="#index">Index</a></li>
            </ul>
        </div>
        <?php echo $SM->get_def("html", "noscript"); ?>
        <div id="content"><div></div></div>
        <div class="JSINJECT-DATA" id="JSINJECT-DATA-index">
            <div>
                <h1 borderleft>Livechat <span class="subtitle_lower" id="chatroomfield"></span></h1>
                <p id="responseField"></p>
                <form action="api.php" method="post" autocomplete="off" enctype="multipart/form-data" id="fileform">
                    <input type="text" name="message" placeholder="Message">
                    <input type="submit" value="Upload File">
                </form>
                <script type="text/javascript">

                    if (window.websocket) {
                        if (window.websocket) {
                            window.websocket.close();
                        }
                    }

                    // Setup the websocket
                    var responseField = document.getElementById('responseField');
                    var chatroomfields = document.getElementById('chatroomfield');
                    var CHAT_PORT = 11231;
                    var ws = new WebSocket(
                        "wss://" +
                        window.location.host + ":" +
                        CHAT_PORT
                    );
                    window.websocket = ws;
                    ws.onopen = function() {
                        if (!window.chatroom) {
                            window.chatroom = null;
                            while (window.chatroom == null) {
                                window.chatroom = prompt('What chatroom do you want to join?');
                            }
                        }
                        ws.send(JSON.stringify({
                            chatroom: window.chatroom
                        }));
                    }

                    ws.onmessage = function(data) {
                        data = JSON.parse(data.data);
                        var message = data.message;
                        var time = new Date().toLocaleString('en-US', {
                            hour12: false
                        }).split(' ')[1];
                        message = time + ": " + message;
                        responseField.innerHTML += message + "<br />";
                        chatroomfield.innerHTML = data.chatroom + " (" + data.users + " clients)";
                    }

                    // Setup the form
                    var form = document.getElementById('fileform');
                    form.onsubmit = function(event) {
                        event.preventDefault();

                        if (ws.readyState != 1) {
                            return false;
                        }

                        ws.send(JSON.stringify({
                            message: event.target.elements[0].value,
                            chatroom: window.chatroom
                        }));

                        form.reset();
                    };
                </script>
            </div>
        </div>
        <?php
        include_once $SM->get_def("presets", "scripts", 2);
        ?>
        <!--  This site was made with love by myself. Copyright © 2015 Leonard Schütz -->
    </body>
</html>
