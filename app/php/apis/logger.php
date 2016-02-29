<?php
require_once __DIR__.'/../main.php';
function logger($message, $domain = 'global') {
	global $SM;
	if ($domain == '') {
		$domain = 'global';
	}
	if ($message != '') {
		// get all filenames of the logs
		$prefix = $SM->get_def('build', 'logs', 2);
		$logs = glob($prefix."*");

		// extract only the basename
		foreach ($logs as $key => $value) {
			$logs[$key] = basename($value);
		}

		// extract the domains
		foreach ($logs as $key => $value) {
			$logs[splitFilename($value)[0]] = $value;
			unset($logs[$key]);
		}

		// check if the file alredy exists
		if (is_file($prefix.$logs[$domain])) {
			$filepath = $prefix.$logs[$domain];
			$f_type = 'write';
		} else {
			$filepath = $prefix.$domain.".txt";
			$f_type = 'create';
		}

		// write to the file
		if (file_put_contents($filepath, format($message), FILE_APPEND)) {
			return $f_type.'_success';
		}
		return $f_type.'_error';
	}
}
/*
	This formats the message
	It adds date, time and some decoration

	It adds \n after every message so that the log is nicelly formatted
*/
function format($m) {
	$dt = new DateTime();
	$dt = $dt->format('Y-m-d-H-i-s');
	return $dt." # ".$m."\n";
}
?>
