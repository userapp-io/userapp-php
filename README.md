# PHP library for UserApp

[![Build Status](https://travis-ci.org/userapp-io/userapp-php.png)](https://travis-ci.org/userapp-io/userapp-php)
[![Code Quality](https://scrutinizer-ci.com/g/userapp-io/userapp-php/badges/quality-score.png?s=4731b8e33e4f5866f28a1b9cafb23b83d39d9792)](https://scrutinizer-ci.com/g/userapp-io/userapp-php/)

## Getting started

### Finding your App Id and Token

If you don't have a UserApp account, you need to [create one](https://app.userapp.io/#/sign-up/).

* **App Id**: The App Id identifies your app. After you have logged in, you should see your `App Id` instantly. If you're having trouble finding it, [follow this guide](https://help.userapp.io/customer/portal/articles/1322336-how-do-i-find-my-app-id-).

*  **Token**: A token authenticates a user on your app. If you want to create a token for your logged in user, [follow this guide](https://help.userapp.io/customer/portal/articles/1364103-how-do-i-create-an-api-token-). If you want to authenticate using a username/password, you can acquire your token by calling `$api->user->login(...);`

### Loading the library

UserApp relies on the autoloading features of PHP to load its files when needed. The autoloading complies with the PSR-0 standard which makes it compatible with most of the major frameworks and libraries. Autoloading in your application is handled automatically when managing the dependencies with [Composer](https://packagist.org/packages/userapp/userapp).
    
#### Using Composer? Add this to your `composer.json`

	{
		"require": {
			"userapp/userapp": "~1.0.0"
		}
	}

#### Not using Composer? Use the library's own autoloader

    require 'UserApp/Autoloader.php';
    UserApp\Autoloader::register();

### Creating your first client

	$api = new \UserApp\API("YOUR APP ID");

#### Additional ways of creating a client

If you want to create a client with additional options  the easiest way is to pass an array with the options as shown below.

	$api = new \UserApp\API(array(
        "debug" => true,
        "throw_errors" => false
    ));

If you pass a string value into the constructor the first argument will be treated as the `App Id`, the second as the `Token`. If you pass an *array* then it will always be treated as additional options. I.e. some valid constructs are:

	$api = new \UserApp\API("MY APP ID");
#
	$api = new \UserApp\API("MY APP ID", array(
        "option" => "some value"
    ));
#
	$api = new \UserApp\API("MY APP ID", "MY TOKEN", array(
        "option" => "some value"
    ));

## Calling services and methods

This client has no hard-coded API definitions built into it. It merly acts as a proxy which means that you'll never have to update the client once new API methods are released. If you want to call a service/method all you have to do is look at the [API documentation](https://app.userapp.io/#/docs/) and follow the convention below:

    $result = $api->[service]->[method](array([argument] => [value]));

#### Some examples

The API [`user.login`](https://app.userapp.io/#/docs/user/#login) and its arguments `login` and `password` translates to:

    $login_result = $api->user->login(array(
        "login" => "test",
        "password" => "test"
    ));

The API [`user.invoice.search`](https://app.userapp.io/#/docs/invoice/#search) and its argument `user_id` translates to:

    $invoices = $api->user->invoice->search(array(
        "user_id" => "test123"
    ));

The API [`property.save`](https://app.userapp.io/#/docs/property/#save) and its arguments `name`, `type` and `default_value` translates to:

    $property = $api->property->save(array(
        "name" => "my new property",
        "type" => "boolean",
        "default_value" => true
    ));

The API [`user.logout`](https://app.userapp.io/#/docs/user/#logout) without any arguments translates to:

    $api->user->logout();

## Configuration

Options determine the configuration of a client. If no options are passed to a client, the options will automatically be inherited from the client global options (`\UserApp\ClientOptions->getGlobal()`).

### Available options

* **Version** (`version`): Version of the API to call against. Default `1`.
* **App Id** (`app_id`): App to authenticate against. Default `null`.
* **Token** (`token`): Token to authenticate with. Default `null`.
* **Debug mode** (`debug`): Log steps performed when sending/receiving data from UserApp. Default: `false`.
* **Secure mode** (`secure`): Call the API using HTTPS. Default: `true`.
* **Base address** (`base_address`): The address to call against. Default: `api.userapp.io`.
* **Throw errors** (`throw_errors`): Whether or not to throw an exception when response is an error. I.e. result `{"error_code":"SOME_ERROR","message":"Some message"}` results in an exception of type `\UserApp\Exceptions\ServiceException`.

### Setting options

Options can either be set in global or local scope. Global is the scope in which all clients inherit their default options from.

#### Global scope

Global options can be set using 

    \UserApp\API::setGlobalOptions(array(
        "app_id" => "MY APP ID",
        "token" => "MY TOKEN"
    )).

### Local scope

The easiest way to set a local scoped option is to do it in the constructor when creating a new client.

    $api = new \UserApp\API(array(
        "debug" => true,
        "throw_errors" => false
    ));

If you want to set an option after the client has been created you can do it as shown below.

    $api->setOption("debug", true);

Setting multiple options is done almost the same way.

    $api->setOptions(array(
        "debug" => true,
        "throw_errors" => false
    ));

## Example code

A more detailed set of examples can be found in /examples.

### Example code (sign up a new user)

    $api = new \UserApp\API("YOUR APP-ID");

    $api->user->save(array(
       "login" => "johndoe81",
       "password" => "iwasfirst!111"
    ));

### Example code (logging in and updating a user)

    $api = new \UserApp\API("YOUR APP-ID");

    $api->user->login(array(
       "login" => "johndoe81",
       "password" => "iwasfirst!111"
    ));

    $api->user->save(array(
       "user_id" => "self",
       "first_name" => "John",
       "last_name" => "Doe"
    ));

	$api->user->logout();

### Example code (finding a specific user)

    $api = new \UserApp\API("YOUR APP-ID", "YOUR TOKEN");

    $search_result = $api->user->search(array(
       "filters" => array(
           "query" => "*bob*"
       ),
       "sort" => array(
           "created_at" => "desc"
       )
    ));

    print_r($search_result->items);

## Versioning

If you want to configure the client to call a specific API version you can do it by either setting the `version` option, or by calling the client using the convention `$api->v[version number]`. If no version is set it will automatically default to `1`.

### Examples

Since no version has been specified, this call will be made against version `1` (default).

    $api->user->login(array("login" => "test", "password" => "test"));

Since the version has been explicitly specified using options, the call will be made against version `2`.

	$api = new \UserApp\API(array("version" => 2));
    $api->user->login(array("login" => "test", "password" => "test"));

Since the version has been explicitly specified, the call will be made against version `3`.

    $api->v3->user->login(array("login" => "test", "password" => "test"));

## Error handling

### Debugging

Sometimes to debug an API error it's important to see what is being sent/received from the calls that one make to understand the underlying reason. If you're interested in seeing these logs, you can set the client option `debug` to `true`, then print the logs after you've made your API call (as shown below).

	$api = new \UserApp\API(array("debug" => true));
    $api->user->login(array("login" => "test", "password" => "test"));
	print_r($api->getLogger()->getLogs());

### Catching errors

When the option `throw_errors` is set to `true` (default) the client will automatically throw a `\UserApp\Exceptions\ServiceException` exception when a call results in an error. I.e.

	try{
		$api->user->save(array("user_id" => "invalid user id"));
	}catch(\UserApp\Exceptions\ServiceException $exception){
		switch($exception->getErrorCode()){
            // Handle specific error
            case "INVALID_ARGUMENT_USER_ID":
				throw new Exception("User does not exist");
			default:
				throw $exception;
        }
	}

Setting `throw_errors` to `false` is more of a way to tell the client to be silent. This will not throw any service specific exceptions. Though, it might throw a `\UserApp\Exceptions\UserAppException`.

	$result = $api->user->save(array("user_id" => "invalid user id"));

	if(is_object($result) && isset($result->error_code)){
		if($result->error_code == "INVALID_ARGUMENT_USER_ID"){
            // Handle specific error
        }
    }

## Solving issues

### See what is being sent to/from UserApp

1. Set the client option `debug` to `true` (see section *options* for more details on setting client options). If no logger is set, this automatically adds a MemoryLogger to your API client. The logger is retrievable using `$api->getLogger()`.
2. Like above, set the option `throw_errors` to `false`. This disables any error exceptions (`\UserApp\Exceptions\ServiceException`) being thrown.
3. Make the API calls that you want to debug. E.g. `$user->user->login(array("login" => "test"));`
4. Print the logs! `print_r($api->getLogger()->getLogs());`
5. Stuck? Send the output to [support@userapp.io](mailto:support@userapp.io) and we'll help you out.

### Common errors

##### Exception: This transport requires that the cURL PHP extension is installed.

You are missing the cURL extension. Follow these instructions to install it: http://askubuntu.com/questions/9293/how-do-i-install-curl-in-php5 (Ubuntu)

##### PHP Fatal error:  Call to undefined function json_encode().

Try executing: `# sudo apt-get install php5-json` (Ubuntu).

## Special cases

Even though this client works as a proxy and there are no hard-coded API definitions built into it, there are still a few tweaks that are API specific.

#### Calling API `user.login` will automatically set the client token

In other words:

	$login_result = $api->user->login(array("login" => "test", "password" => "test"));

Is exactly the same as:
	
	$login_result = $api->user->login(array("login" => "test", "password" => "test"));
	$api->setOption("token", $login_result->token);

#### Calling API `user.logout` will automatically remove the client token

In other words:

	$api->user->logout();

Is exactly the same as:
	
	$api->user->logout();
	$api->setOption("token", null);

## Dependencies

* PHP >= 5.3.2
* [cURL](http://php.net/manual/en/book.curl.php)

## License

MIT - For more details, see LICENSE.
