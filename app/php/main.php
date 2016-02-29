<?php
/*
	Include the main frameworks
*/
require_once 'apis/siteManager.php';
require_once 'apis/commons.php';
require_once 'apis/logger.php';
$SM = new siteManager; //get a site manager

/*
	Create an array from the given parameters
*/
function article($title, $content, $subtitle) {
	$article = ['title'=>$title, 'content'=>$content, 'subtitle'=>$subtitle];
	return $article;
}

/*
	Generate html code for given data and category
*/
function gen_html($data, $pre = false, $attributes=[]) {
	//Get the manifest
	if (!is_array($data)) {
		$manifest = manifest($data, $pre);
	} else {
		$manifest = $data;
	}

	// if the manifest is false, don't show the article
	if ($manifest) {
		// different behaviour if the manifest is already html
		if (is_array($manifest)) {
			// parse the custom tags
			$manifest = parse($manifest, ["name"=>$data, "pre"=>$pre]);

			$html="<div>";
			if (!$attributes["subtitle_lower"]) {
				$html.="<h1 borderleft>".$manifest["title"]." <span class=\"subtitle_lower\">".$manifest["subtitle"]."</span></h1>";
			} else {
				$html.="<h1 borderleft>".$manifest["title"]." <span>".$manifest["subtitle"]."</span></h1>";
			}
			$html.="<p>".$manifest["content"]."</p>";
			$html.="</div>";
		} else {
			$html = $manifest;
		}

		// stick the html together
		return $html;
	} else {
		// invalid manifest
		return false;
	}
}

/*
	Request a manifest for a given source name and type
*/
function manifest($source, $pre = false) {
	global $SM;
    if (is_string($pre)) {
        $type = $pre;
    } else {
    	$type = ($pre ? 'pre-written':'blog');
    }
	// request the json manifest using jsonForfile from commons
	$urlprefix = $SM->get_def("article", $type, 2);
	$manifest = false;
	if (file_exists($urlprefix.$source."/article.html")) {
		$manifest = file_get_contents($urlprefix.$source."/article.html");
	} elseif (file_exists($urlprefix.$source."/article.json")) {
		$manifest = json_decode(file_get_contents($urlprefix.$source."/article.json"), 1);
	} else {
		$manifest = file_get_contents($SM->get_def("article", "pre-written", 2)."sectionnotfound/article.html");
	}

    // Replace all occurences of "%%THIS%%" with the current path
    $manifest = str_replace('%%THIS%%', $SM->get_def("article", $type, 1).$source, $manifest);

	return $manifest;
}

/*
	Generate the html for a given section of the site
*/
function serve($show) {
	global $SM;

	// Piece the html together
	$html = "";
	switch ($show) {
		case 'projects':
			//Get the projects manifest
			$projectsManifest = json_decode(file_get_contents($SM->get_def('project', 'manifest', 2)), true);
			$projectsManifest = multarray_sort($projectsManifest, 'datesort');

			//Show a card for every project
			$projectsCount = count($projectsManifest);
			for ($i=0; $i <= $projectsCount-1; $i++) {
				$isShown = 	$projectsManifest[$i]['show'];
				if ($isShown) {
					//Collect the info
					$title = 	$projectsManifest[$i]['title'];
					$content = 	$projectsManifest[$i]['content'];
					$subtitle = $projectsManifest[$i]['subtitle'];
					$location = $projectsManifest[$i]['location'];

					//Make the title a link
					if (is_array($location)) {
						$title = "#a#".$location['url']." - ".$title."#a#";
					} else {
						$title = "#a#".$SM->get_def('project', 'root').$location." - ".$title."#a#";
					}

					//Show the card
					$html .= gen_html(article($title, $content, $subtitle));
				}
			}
			break;
		case 'about':
			//Show the aboutme card
			$html .= gen_html('aboutme', true);
			break;
		case 'blog':
			// blog items
			$dir = array_reverse(glob($SM->get_def('article', 'blog', 2)."*"));
			foreach ($dir as $key => $pre) {
				$name_parts = explode("/", $pre);
				$name = $name_parts[count($name_parts)-1];
				$html .= trim(gen_html($name));
			}
			break;
        case 'editor':
            // editor items
            $dir = array_reverse(glob($SM->get_def('article', 'editor', 2)."*"));
            foreach ($dir as $key => $pre) {
                $name_parts = explode("/", $pre);
                $name = $name_parts[count($name_parts)-1];
                $html .= trim(gen_html($name, 'editor'));
            }
            break;
		default:
			// section not found
			$html .= gen_html($show);
			break;
	}

	//Return the html string
	return $html;
}

/*
	Get the contents of two given tags inside a string
*/
function contentsOfTag($tag, $value) {
	$a = true;
	$firstRun = true;
	$parsedString = "";
	$contentsArray = [];

	while ($a) {
		//Used to parse multiple tags per text
		if ($firstRun) {
			$string = " ".$value;
			$firstRun = false;
		} else {
			$string = " ".$parsedString;
		}

		//Search for the string
		$startpos = strpos($string, $tag);

		// I've added an additional whitespace at the beginning of the text to make sure we catch tags that are at the beginning of the string
		if ($startpos != 0) {
			//Move startpos to the beginning of the content of the tag
			$startpos += strlen($tag);

			//Searches for the next tag, and returns the distance between it and startpos
			if (strpos($string, $tag, $startpos) - $startpos > 0) {
				$length = strpos($string, $tag, $startpos) - $startpos;
			} else {
				//No closing tag found
				$a = false;
			}

			//Get's the content of the tag
			$tagcontent = substr($string, $startpos, $length);

			//Trims the content, to remove unwanted whitespaces
			$tagcontentTrimmed = trim($tagcontent);

			//Add the tag content to the array
			array_push($contentsArray, $tagcontentTrimmed);

			//Remove the tag and the content to search for more tags of the same type
			if ($closingTag[0]) {
				$deformatedTagString = $tag.$tagcontent.$closingTag[1];
			} else {
				$deformatedTagString = $tag.$tagcontent.$tag;
			}

			$parsedString = str_replace($deformatedTagString, "___", $string);
		} else {
			$a = false;
		}
	}

	// return false if the contentsarray is empty.
	if (count($contentsArray) != 0) {
		return $contentsArray;
	} else {
		return false;
	}
}

/*
	Parses $data
	If $data is an array, every child will be parsed
*/
function parse($data, $source = []) {
	if (gettype($data) == "string") {
		$data = [$data];
	}
	foreach ($data as $key => &$value) {
		if (str_contains('#hl#', $value)) {
			$value = parseTag('#hl#', $value, '<span class="highlight">', '</span>');
		}
		if (str_contains('#qo#', $value)) {
			$value = parseTag('#qo#', $value, '#nl#<blockquote>', '</blockquote>#nl#');
		}
		if (str_contains('#a#', $value)) {
			$value = parseTag('#a#', $value);
		}
		if (str_contains('#img#', $value)) {
			$value = parseTag('#img#', $value, 'undefined', 'undefined', $source);
		}
		if (str_contains('#video#', $value)) {
			$value = parseTag('#video#', $value, 'undefined', 'undefined', $source);
		}
		$value = str_replace('#nl#', '<br/>', $value);
		$value = str_replace('#np#', '<br/><br/>', $value);
	}
	if (count($data) == 1) {
		$data = $data[0];
	}
	return $data;
}

/*
	If a prefix or suffix is given, return the parsed string based on that
*/
function parseTag($tag, $string, $prefix = 'undefined', $suffix = 'undefined', $source = []) {
	global $SM;

	//Check if at least one tag exists
	$exists = str_contains($tag, $string);

	//Check if tags exist
	if ($exists) {
		//Get the contents of the tag
		$tagContents = contentsOfTag($tag, $string);

		//Check if contentsOfTag returned an Error
		if (isset($tagContents['Error'])) {
			return false;
		}

		//Replace
		foreach($tagContents as $tagcontent) {
			$search = $tag.$tagcontent.$tag;

			//Diferent behaviour for complicated tags
			if ($tag == "#img#" or $tag == "#video#") {

				//Explode and trim the tagcontent
				$tagelements = [];
				foreach(explode(' - ', $tagcontent) as $tagelement) {
					array_push($tagelements, trim($tagelement));
				}

				//check if there are multiple arguments
				$secondparam = false;
				if (count($tagelements) > 1) {
					$secondparam = $tagelements[1];
				}

				$filename = trim($tagelements[0]);
				$filepath = "";
				$filepath = $SM->get_res($filename, $source['name'], ($source['pre'] ? 'pre-written' : 'blog'));
				if ($tag == "#img#") {
					$replace = $replace = '<img src="/'.$filepath.'"/>';
				} else {
					$replace = $replace = '<video src="/'.$filepath.'" controls preload="none"/>';
				}

			} else if ($tag == "#a#") {
				//Explode and trim the tagcontent
				$tagelements = [];
				foreach(explode(' - ', $tagcontent) as $tagelement) {
					// fix for the interpreter not resolving additional minus-signs.
					if (count($tagelements) != 2) {
						array_push($tagelements, trim($tagelement));
					} else {
						$tagelements[1] .= " - ".$tagelement;
					}
				}

				$elementcount = count($tagelements);
				if ($elementcount == 1) {
					$replace = '<a href="'.$tagelements[0].'">Link</a>';
				} elseif ($elementcount == 2) {
					$replace = '<a href="'.$tagelements[0].'">'.$tagelements[1].'</a>';
				}
			} else {
				//Default behaviour
				if ($prefix === 'undefined') {
					$replace = trim($tagcontent).$suffix;
				} elseif ($suffix === 'undefined') {
					$replace = $prefix.trim($tagcontent);
				} elseif ($prefix === 'undefined' && $suffix === 'undefined') {
					$replace = trim($tagcontent);
				} else {
					$replace = $prefix.trim($tagcontent).$suffix;
				}
			}

			//Replace
			$string = str_replace($search, $replace, $string);
		}
	}

	//Return
	return $string;
}
?>
