<title><?php echo $SM->get_def("meta", "title", 0); ?></title>
<meta charset="utf-8">
<meta name="viewport" content="<?php echo $SM->get_def("meta", "viewport", 0); ?>">
<meta name="author" content="<?php echo $SM->get_def("meta", "author", 0); ?>">
<meta name="description" content="<?php echo $SM->get_def("meta", "description", 0); ?>">
<link rel="stylesheet" type="text/css" href="<?php echo $SM->get_def("css", "main", 1); ?>">
<link rel="icon" type="image/png" href="<?php echo $SM->get_def("asset", "favicon", 1); ?>"/>
<style media="screen" id="critical">
	/* critical css, get's removed later */
	#hero {
		height: 100vh !important;
	}
	#hero>div {
		opacity: 0;
	}
	#hero>ul {
		opacity: 0;
	}
</style>
