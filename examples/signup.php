<?php

	require("config.php");

	$api = new \UserApp\API(USERAPP_APP_ID);

	try
	{
		$user_result = $api->user->save(array(
			"login" => "epicrawbot",
			"email" => "epicrobot@userapp.io",
			"password" => "play_withBitz!"
		));

		echo(sprintf("Saved new user %s with user id %s.<br />\n",
			$user_result->login, $user_result->user_id));
	}
	catch(\UserApp\Exceptions\ServiceException $exception)
	{
		echo(sprintf("An error occurred: %s (%s).<br />\n", $exception->getMessage(), $exception->getErrorCode()));
	}

?>