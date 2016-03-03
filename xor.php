<?php
require_once('app/php/main.php');
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
                <h1>XOR Cipher</h1>
            </div>
            <ul>
                <li><a id="start" href="#start">Start</a></li>
            </ul>
        </div>
        <?php echo $SM->get_def("html", "noscript"); ?>
        <div id="content"><div></div></div>
        <div class="JSINJECT-DATA" id="JSINJECT-DATA-start">
            <div>
                <h1 borderleft>Encrypt / Decrypt</h1>
                <form id="mainform">
                    <textarea type="text" placeholder="Message to encrypt"></textarea>
                    <input type="text" placeholder="Key to use">
                    <input type="submit" value="Encrypt / Decrypt">
                </form>
                <pre id="output" style="word-wrap: break-word; overflow:hidden"></pre>
                <script>
                    var form = document.getElementById('mainform');
                    var output = document.getElementById('output');
                    form.onsubmit = function(event) {
                        event.preventDefault();

                        // Get the needed values
                        var message = event.target[0].value;
                        var key = event.target[1].value;

                        // Turn them into binary numbers
                        var binary = [message, key].map(function(set) {
                            return set.split('').map(function(char) {
                                var binary = char.charCodeAt(0).toString(2);
                                while ((8 - binary.split('').length) != 0) {
                                    binary = '0'+binary;
                                }
                                return binary;
                            });
                        });
                        message = binary[0];
                        key = binary[1];

                        // Apply all the keys letters
                        key.map(function(kChar) {
                            message = message.map(function(mChar) {
                                mChar = mChar.split('').map(function(bin, i) {
                                    return (kChar[i]==bin ? '1' : '0');
                                }).join('');
                                return mChar;
                            });
                        });

                        // Stick the string back together
                        message = message.map(function(char) {
                            var charCode = parseInt(char, 2);
                            return String.fromCharCode(charCode);
                        }).join('');

                        output.innerHTML = message;
                    }
                </script>
            </div>
        </div>
        <?php
        include_once $SM->get_def("presets", "scripts", 2);
        ?>
        <!--  This site was made with love by myself. Copyright © 2015 Leonard Schütz -->
    </body>
</html>
