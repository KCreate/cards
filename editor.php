<?php
require_once('app/php/main.php');

// Check if there are post parameters
if (count($_POST) > 0) {
    // Check if the needed parameters are set
    if (!(isset($_POST['newarticlename']) && isset($_POST['password']))) {
        die('Missing parameters');
    }

    // Check if the password is correct
    if (!login($_POST['password'])) { die('Wrong username or password'); }

    // Get the date part of the filename
    $date = date('Y_m_d_');

    // Append the picked filename
    $filename = $date.strtolower($_POST['newarticlename']);
    $filename = str_replace(' ', '', $filename);

    // Create the filepath
    $filename = $SM->get_def('article', 'editor', 2).$filename;

    // Create the new directory
    mkdir($filename, 0766);

    // Create a new template article.html file
    $writestate = file_put_contents("$filename/article.html", '
<div>
    <h1 borderleft>'.str_replace(' ', '', $date.strtolower($_POST['newarticlename'])).' <span class="subtitle_lower">Template subtitle</span></h1>
    <p>
        Template message
    </p>
</div>
    ');  

    die($writestate ? 'Successfully added new blog article' : 'Failed to create new blog article');
}

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
                <li><a id="editor" href="#editor">Editor</a></li>
            </ul>
        </div>
        <?php echo $SM->get_def("html", "noscript"); ?>
        <div id="content"><div></div></div>
        <div class="JSINJECT-DATA" id="JSINJECT-DATA-editor" hasRemoteSource>
            <div>
                <h1 borderleft>Create new blog article</h1>
                <form id="newarticleform" autocomplete="off">
                    <input type="text" name="newarticlename" placeholder="Article name"></input>
                    <input type="password" name="password" placeholder="Password"></input>
                    <input type="submit" value="Submit"></input>
                </form>
                <script type="text/javascript">
                    var form = document.getElementById('newarticleform');
                    form.onsubmit = function(event) {
                        event.preventDefault();

                        // Setup the formdata
                        var formdata = new FormData();
                        for (var i=0; i<form.elements.length; i++) {
                            formdata.append(form.elements[i].name, form.elements[i].value);
                        }

                        // Setup an xhr
                        var xhr = new XMLHttpRequest();
                        xhr.onreadystatechange = function() {
                            if (xhr.readyState == 4 && xhr.status == 200) {
                                alert(xhr.response);
                                form.reset();
                                window.location.reload();
                            }
                        }
                        xhr.open('POST', 'editor.php', true);
                        xhr.send(formdata);
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
