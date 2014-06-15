<?php

	$loader = require __DIR__ . "/../vendor/autoload.php";
	$loader->addPsr4('UserApp\\', __DIR__.'/UserApp');

    require(dirname(__FILE__) . "/Core/TestTransport.php");

?>