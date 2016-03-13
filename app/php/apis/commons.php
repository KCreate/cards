<?php
/*
    Overly simple login system, not meant to be taken seriously
*/
function login($p) {
    // Place the password hash here
    return password_verify($p, '$2y$14$u19ti7nzFsB0no6WM544s.5Nl7I6g24xQrBtGRbuRyNIOnEaWyFL6');
}
/*
	Returns true if $text contains the string $search
*/
function str_contains($search, $text) {
	//Return false if $text doesn't contain $search
	$strpos = strpos($text, $search);
	return ($strpos !== false ? true : false);
}

/*
	Sort an array based on a key inside all of its childs
*/
function multarray_sort($array, $on){
	$new_array = array();
	$sortable_array = array();

	if (count($array) > 0) {
		foreach ($array as $k => $v) {
			if (is_array($v)) {
				foreach ($v as $k2 => $v2) {
					if ($k2 == $on) {
						$sortable_array[$k] = $v2;
					}
				}
			} else {
				$sortable_array[$k] = $v;
			}
		}

		natsort($sortable_array);

		foreach ($sortable_array as $k => $v) {
			$new_array[$k] = $array[$k];
		}
	}

	return array_reverse($new_array);
}

// returns the name of a month
function get_month_name($number) {
	if ($number==1 || $number=="01") {
		return "January";
	}
	if ($number==2 || $number=="02") {
		return "February";
	}
	if ($number==3 || $number=="03") {
		return "March";
	}
	if ($number==4 || $number=="04") {
		return "April";
	}
	if ($number==5 || $number=="05") {
		return "May";
	}
	if ($number==6 || $number=="06") {
		return "June";
	}
	if ($number==7 || $number=="07") {
		return "July";
	}
	if ($number==8 || $number=="08") {
		return "August";
	}
	if ($number==9 || $number=="09") {
		return "September";
	}
	if ($number==10 || $number=="10") {
		return "October";
	}
	if ($number==11 || $number=="11") {
		return "November";
	}
	if ($number==12 || $number=="12") {
		return "December";
	}
	return "January";
}

/*
	Check if a given value is in JSON
*/
function isJson($string) {
	json_decode($string);
	return (json_last_error() == JSON_ERROR_NONE);
}

/*
	Splits the extension from the filename
	written by pai.ravi@yahoo.com
*/
function splitFilename($filename) {
    $pos = strrpos($filename, '.');
    if ($pos === false) { // dot is not found in the filename
        return array($filename, ''); // no extension
    } else {
        $basename = substr($filename, 0, $pos);
        $extension = substr($filename, $pos+1);
        return array($basename, $extension);
    }
}

/* returns the size of a file in a human readable format */
function human_filesize($bytes, $decimals = 2) {
    $size = array('B','kB','MB','GB','TB','PB','EB','ZB','YB');
    $factor = floor((strlen($bytes) - 1) / 3);
    return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$size[$factor];
}
?>
