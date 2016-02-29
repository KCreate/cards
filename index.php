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
                <h1></h1>
            </div>
            <ul>
                <li><a id="blog" href="#blog">Blog</a></li>
                <li><a id="projects" href="#projects">Projects</a></li>
                <li><a id="about" href="#about">About</a></li>
            </ul>
        </div>
        <?php echo $SM->get_def("html", "noscript"); ?>
        <div id="content"><div></div></div>
        <?php
        include_once $SM->get_def("presets", "scripts", 2);
        ?>
        <!--  This site was made with love by myself. Copyright © 2015 Leonard Schütz -->
    </body>
</html>
