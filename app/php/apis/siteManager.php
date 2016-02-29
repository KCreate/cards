<?php
if (isset($_GET['ajax'])) {
	if (isset($_GET['param'])) {
		$sm = new siteManager; // get a site manager

		// divide the params into their components
		$param = explode("*", $_GET['param']);

		// return the wanted parameter
		echo $sm->get_def($param[0], $param[1], $param[2]);
	} else {
		echo "no param specified.";
	}
}

class siteManager
{
	var $SM_DEFINITIONS;

	// construct function
	public function __construct()
	{
		// get the definitions from the json file
		$this->SM_DEFINITIONS = file_get_contents(__DIR__.'/sm-defs.json');
		if (is_string($this->SM_DEFINITIONS))
		{
			$this->SM_DEFINITIONS = json_decode($this->SM_DEFINITIONS, 1);
		}
	}

	public function get_prefix() {
		$server_name = $_SERVER['SERVER_NAME'];
		return "https://".$server_name."/";
	}

	// get images inside articles
	public function get_res($resName, $articleName, $category)
	{
		$path  = $this->get_prefix().$this->get_def('article', $category);
		$path .= $articleName."/".$resName;
		return $path;
	}

	// resource locator
	public function get_def($type, $resource, $pathmode = 0)
	{
		if (isset($this->SM_DEFINITIONS[$type][$resource])) {
			if ($pathmode == 2) { // intern
				$path = $_SERVER['DOCUMENT_ROOT']."/".$this->SM_DEFINITIONS[$type][$resource];
			} elseif ($pathmode == 1) { // extern
				$path = $this->get_prefix().$this->SM_DEFINITIONS[$type][$resource];
			} else { // plain
				$path = $this->SM_DEFINITIONS[$type][$resource];
			}
			return $path;
		} else {
			return false;
		}
	}
}
?>
