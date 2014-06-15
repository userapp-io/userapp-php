<?php

	$loader = require __DIR__ . "/../vendor/autoload.php";
	$loader->addPsr4('UserApp\\', __DIR__.'/UserApp');

	// Here you put your App ID and token.
	// Any questions? Email us at support@userapp.io

	define("USERAPP_APP_ID", "YOUR APP ID");
	define("USERAPP_TOKEN", "YOUR TOKEN");

?>