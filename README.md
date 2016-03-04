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
```php
Maenbn\OpenAmAuth\OpenAmAuthServiceProvider::class
```

## Configuration

A configuration for your OpenAM/OpenSSO server is required for the OpenAmAuth to work. First publish all vendor assets:

```bash
$ php artisan vendor:publish
```
which will create a `config/openam.php` file in your app where you can modify it to reflect 
your OpenAM server. If you are using an OpenSSO server, you will need to specify `legacy` as `true`.

Finally make sure to change the value for the `driver` key to `openam` in `config/auth.php`.

### Eloquent model
There is also an option to use an Eloquent model as the user object for OpenAM authentication. This is useful if 
you want to authenticate against OpenAM but want to control authorisation within Laravel e.g. using 
[Entrust](https://github.com/Zizaco/entrust) package. 
 
Ideally the default ```App\User``` class found in a new install of Laravel is perfect for this. Modify the 
`eloquentModel` key in `config/openam.php` to refer to the Eloquent class you want e.g.

```php
'eloquentModel' => App\User::class
```

Your Eloquent model database table will need to contain a field which will contain the OpenAM `uid` for each user in 
order for the match to be made. By default the field it will look for will be named `uid` but you can change that within 
`config/openam.php` file under the key `eloquentUidField`.

Finally, modify your Eloquent model to use the OpenAM `Authenicatable` trait like below:

```php
namespace App;

// Usually this is "use Illuminate\Auth\Authenticatable;"
// Change it to the following line below
use Maenbn\OpenAmAuth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

class User extends Model implements AuthenticatableContract, CanResetPasswordContract
{
    use Authenticatable, CanResetPassword;
    ..........
```

## Middleware

If you require your app to set a cookie to hold the OpenAM token, you can utilise the middleware available in this package. Add it to your `app/Http/Kernel.php` either as a global or route middleware

```php
protected $middleware = [
    ...............
    \Maenbn\OpenAmAuth\Middleware\SetOpenAmCookie::class
];

// Or

protected $routeMiddleware = [
    ...............
    'openamauth.cookie' => \Maenbn\OpenAmAuth\Middleware\SetOpenAmCookie::class
];
```

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
```
