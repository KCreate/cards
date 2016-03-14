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
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
		return $protocol.$server_name."/";
	}

    // Get a static reference to the current directory
    public function static_reference() {
        return realpath(__DIR__."/../../../");
    }

    // Get the path that leads from the document root to the static_reference root
    public function leading_dir() {

        // Explode the document root
        $dcroot = explode("/", $_SERVER['DOCUMENT_ROOT']);

        // Explode the static reference
        $srroot = explode("/", $this->static_reference());

        // Compare the two
        $path = "";
        for ($i=0; $i < count($srroot) - count($dcroot); $i++) {

            $path .= $srroot[count($dcroot)+$i];
        }

        return $path;
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
			if ($pathmode == 2) { // Internal links, includes filesystem paths
				$path = $this->static_reference()."/".$this->SM_DEFINITIONS[$type][$resource];
			} elseif ($pathmode == 1) { // External links
				$path = $this->get_prefix().$this->leading_dir()."/".$this->SM_DEFINITIONS[$type][$resource];
			} else { // Print exact value as defined
				$path = $this->SM_DEFINITIONS[$type][$resource];
			}
			return $path;
		} else {
			return false;
		}
	}
}
?>
