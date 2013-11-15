<?php

	require("../autoload.php");
	require("config.php");

	$api = new \UserApp\API("51ded0be98035", array(
		"base_address" => "192.168.199.101:8888",
		"debug" => true,
		"secure" => false
	));

	$api->setTransport(new \UserApp\Http\CurlTransport(false, $api->getLogger()));

	try
	{
		$login_result = $api->user->login(array(
			"login" => "root",
			"password" => "root"
		));

		echo(sprintf("Authenticated using token %s and user id %s.<br />\n",
			$login_result->token, $login_result->user_id));

		$users = $api->user->get(array(
			'user_id' => array(
				'5Hqt69cKT4uDSQfoYrbs6w',
				'P5-HZPEqR_GaztgO3fF64w',
				'ulZcVIhLQTubzQR0eX6s0Q'
			)
		));

		print_r($users);

		$api->user->logout();
	}
	catch(\UserApp\Exceptions\ServiceException $exception)
	{
		echo(sprintf("An error occurred: %s (%s).<br />\n", $exception->getMessage(), $exception->getErrorCode()));
	}

	print_r($api->getLogger()->getLogs());

?>