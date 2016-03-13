<?php
/*
	Include the main frameworks
*/
require_once 'apis/siteManager.php';
require_once 'apis/commons.php';
$SM = new siteManager; // get a site manager

/*
	Create an object from the given parameters
*/
function article($title, $content, $subtitle) {
	$article = ['title'=>$title, 'content'=>$content, 'subtitle'=>$subtitle];
	return $article;
}

/*
	Generate html code for given data and category
*/
function gen_html($articlename, $section) {
    global $SM;

    // Check if the category exists
    if (file_exists($SM->get_def('article', 'root', 2).$section) &&
        is_dir($SM->get_def('article', 'root', 2).$section)) {

        // Check if the article exists
        if (file_exists($SM->get_def('article', 'root', 2).$section."/".$articlename) &&
            is_dir($SM->get_def('article', 'root', 2).$section."/".$articlename)) {

            // Check if the article.html file exists
            if (file_exists($SM->get_def('article', 'root', 2).$section."/".$articlename."/article.html")) {

                $path = $SM->get_def('article', 'root', 2).$section."/".$articlename."/article.html";

                // Get the contents of the file
                $filecontent = file_get_contents($path);

                // Replace all occurences of "%%THIS%%" with the article path
                $filecontent = str_replace(
                    '%%THIS%%',
                    $SM->get_def('article', 'root', 2).$section."/".$articlename,
                    $filecontent
                );

                // Return
                return $filecontent;
            }
        }
    }

    // If an error happened, show the error card
    gen_html('articlenotfound', 'app');
}

/*
	Generate the html for a given section of the site
*/
function serve($show) {
    global $SM;

	// Check the $show contains a *
    if (str_contains("*", $show)) {
        $show = explode("*", $show);
    } else {
        $show = [$show];
    }

	$html = "";

    // Inject custom content here
	switch ($show[0]) {
		default:
			break;
	}

    // Check if a specific article was desired
    if (count($show)==2) {

        // Only render this one article
        $html .= gen_html($show[0], $show[1]);
    } else {

        // Get all articles inside this directory
        $articles = glob($SM->get_def('article', 'root', 2).$show[0]."/*");
        foreach ($articles as $key => $value) {

            // Check if there is a article.html file inside this directory
            if (file_exists($value."/article.html")) {

                // Render the article
                $html .= gen_html(basename($value), $show[0]);
            }
        }
    }

	// Return the html string
	return $html;
}

?>
