# OpenAM Authentication 
=================
This is a provider for adding a OpenAM/OpenSSO driver to your authentication system in Laravel 5

## Installation

The tool requires you have [PHP](https://php.net) 5.4.*+ and [Composer](https://getcomposer.org).

You will also need a [OpenAM](http://openam.forgerock.org/) or [OpenSSO](https://java.net/projects/opensso)
server.

The get the latest version of OpenAM Authentication, add the following line to your `composer.json` file:
```
"maenbn\openamauth": "dev-master"
```

Then run `composer install` or `composer update` to install.

You will also need to register the service provider by going into `config/app.php` and add the following to the `providers` key:
```
'Maenbn\OpenAmAuth\OpenAmAuthServiceProvider'
```

## Configuration

A configuration for your OpenAM/OpenSSO server is required for the OpenAmAuth to work. First publish all vendor assets:

```bash
$ php artisan vendor:publish
```
which will create a `config/openam.php` file in your app where you can modify it to reflect 
your OpenAM server. If you are using an OpenSSO server, you will need to specify `legacy` as `true`.

##Usage
Now your Auth driver is using OpenAM you will be able to use the Laravel's `Auth` class to authenticate users.

###Examples

```php
//Authenticating using the OpenAM TokenID from a cookie
Auth::attempt();
	
//Authenticating using user input
$input = Input::only('username', 'password');
Auth::attempt($input);

//Retrieving the OpenAM attributes of a logged in user
$user = Auth::user();
