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
                <h1></h1>
            </div>
            <ul>
                <li><a id="upload" href="#upload">Upload</a></li>
            </ul>
        </div>
        <?php echo $SM->get_def("html", "noscript"); ?>
        <div id="content"><div></div></div>
        <div class="JSINJECT-DATA" id="JSINJECT-DATA-upload">
            <div>
                <h1 borderleft>Upload</h1>
                <p>
                    All documents will get placed inside the <a href="/storage/documents">documents folder</a>.
                </p>
                <p id="responseField"></p>
                <form action="api.php" method="post" autocomplete="off" enctype="multipart/form-data" id="fileform">
                    <input type="file" name="files" placeholder="File" multiple>
                    <input type="password" name="key" placeholder="Password">
                    <input type="submit" value="Upload File">
                </form>
                <script type="text/javascript">
                    var form = document.getElementById('fileform');
                    var responseField = document.getElementById('responseField');
                    form.onsubmit = function(event) {
                        event.preventDefault();

                        var files = event.srcElement[0].files;
                        var formData = new FormData();

                        // Check if there are any files, and if a password was entered
                        if (files.count == 0 || event.srcElement[1].value == '') {
                            responseField.innerHTML = "Select some files and write your password";
                            return false;
                        }

                        // Update the responseField
                        responseField.innerHTML = 'Uploading...';

                        // Get all the files
                        formData.append('key', event.srcElement[1].value);

                        // Iterate over all files
                        for (var i=0; i<files.length; i++) {
                            formData.append('files[]', files[i], files[i].name);
                        }

                        // Set up the request
                        var xhr = new XMLHttpRequest();
                        xhr.open("POST", 'api.php', true);
                        xhr.upload.addEventListener("progress", function(e) {
                            responseField.innerHTML = parseInt((100 / e.total) * e.loaded) + "% transfered...";
                        })
                        xhr.onreadystatechange = function() {
                            if (xhr.status == 200 && xhr.readyState == 4) {
                                // We've got a response from the server
                                var response = xhr.responseText.trim();

                                if (response == 'error_saving') {
                                    responseField.innerHTML = 'There was a problem saving the file serverside';
                                } else if (response == 'error_uploading') {
                                    responseField = 'There was a problem uploading the file';
                                } else if (response == 'error_wrong_password') {
                                    responseField.innerHTML = "The password you've entered was incorrect.";
                                } else if (response == 'upload_successful') { // Sometimes the server responds with random gibberish
                                    responseField.innerHTML = 'Upload was successful';
                                }
                            } else {
                                responseField.innerHTML = 'An error occured while upload';
                            }

                            // Reset all fields
                            form.reset();
                        }
                        xhr.send(formData);
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

