<?php
require_once 'main.php';
header("Content-Type: text/html");

if (isset($_GET['show'])) {
	$r = $_GET['show'];
	$r = explode("*", $r = trim($r, "*")); // trim and explode the request string
	$r = array_unique($r);

	// output the data
	foreach ($r as $v) {
		echo serve($v);
		if (count($r) > 1) {
			echo "*#*DELIMITER*#*";
		}
	}
}
?>
