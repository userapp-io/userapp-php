<?php

	require("config.php");

	$api = new \UserApp\API(USERAPP_APP_ID);

	try
	{
		$login_result = $api->user->login(array(
			"login" => "jdoe81",
			"password" => "joelikesfishi_g"
		));

		echo(sprintf("Authenticated using token %s and user id %s.<br />\n",
			$login_result->token, $login_result->user_id));

		$user = current($api->user->get());

		echo(sprintf("Authenticated as user %s, first name = %s, last name = %s, email = %s.<br />\n",
			$user->login, $user->first_name, $user->last_name, $user->email));

		$api->user->logout();
	}
	catch(\UserApp\Exceptions\ServiceException $exception)
	{
		echo(sprintf("An error occurred: %s (%s).<br />\n", $exception->getMessage(), $exception->getErrorCode()));
	}

?>