<?php

	require("config.php");

	$api = new \UserApp\API(USERAPP_APP_ID, array(
		"debug" => true,
		"throw_errors" => false
	));

	$user_result = $api->user->login(array(
		"login" => "epicrawbot",
		"password" => "play_withBitz!"
	));

	if($api->hasLogger()){
		print_r($api->getLogger()->getLogs());
	}

?>