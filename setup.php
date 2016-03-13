<?php
/*
The MIT License (MIT)
Copyright (c) <year> <copyright holders>

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
*/

/*
    Instructions on how to use this file:
    Just call the file from any standard web browser or command-line.

    This file creates all the directories that this project needs.
    After the process has finished, this file will disable itself.
*/

###DISABLED###
$disabled = false;
###DISABLED###
if ($disabled) {
    die("The setup process has already finished.");
}

// Create basic storage system
if (!is_dir('storage')) { mkdir('storage'); }
if (!is_dir('storage/articles')) { mkdir('storage/articles'); }
if (!is_dir('storage/articles/app')) { mkdir('storage/articles/app'); }
if (!is_dir('storage/documents')) { mkdir('storage/documents'); }

// Create the robots.txt file
file_put_contents('robots.txt', '
User-agent: *
Disallow: /app/
Disallow: /storage/');

// Disable the file
$filecontents = file_get_contents('setup.php');
$filecontents = explode('###'.'DISABLED'.'###', $filecontents);
$filecontents[1] = '###'.'DISABLED'.'###
$disabled = true;
###'.'DISABLED'.'###';
$filecontents = join('', $filecontents);
file_put_contents('setup.php', $filecontents);

?>
