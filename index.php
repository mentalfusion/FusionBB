<?php
	// include our factory class; this will be used to facilitate just about all communications for the board
	include ('system/config.php');
	include ('system/Factory.Class.php');

	// create our fusionbb factory object
	$fusionbb = new Factory($conf);

	// this variable will be in a config file eventually; for now, manually set it.
	// This is the default action for the index.php file; if we've gotten this far, we need to render a view:
	$view = $fusionbb->getView();
	require ($fusionbb->base_dir.'views/'.$view.'.php');

	$page = new $view($fusionbb);
	$page->render();

?>
<hr />If this line shows, everything is OK.
