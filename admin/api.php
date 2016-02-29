<?php

require_once('../app/php/main.php');

$password = $_POST['key'];
$files = $_FILES;

if (login($password)) {
    // Iterate over all the files
    foreach ($files['files']['name'] as $index => $name) {

        // Skip the file if there was an error
        if ($files['files']['error'][$index]) { continue; }

        //construct the filepath
        $documentroot = $SM->get_def('build', 'documents', 2);
        $dir = $documentroot.join('_', explode(' ', basename($name)));

        while(file_exists($dir)) {
            $dir = $documentroot.join('_', explode(' ', rand(0,100000).'_'.basename($name))); // After 100000 uploaded files this will break really bad...
        }

        $status = move_uploaded_file($files['files']['tmp_name'][$index], $dir);
        if ($status) {
            // log
            logger("Saved file: ".$name, 'uploader');
        }
    }

    // Check if there was an error
    echo "upload_successful";
} else {
    if ($password!==NULL) {
        echo "error_wrong_password";
    }
}

?>

