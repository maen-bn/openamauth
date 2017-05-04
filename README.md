# OpenAM Authentication 
[![Build Status](https://travis-ci.org/maen-bn/openamauth.svg?branch=3.0)](https://travis-ci.org/maen-bn/openamauth)
[![Codecov](https://img.shields.io/codecov/c/github/maen-bn/openamauth.svg)](https://codecov.io/gh/maen-bn/openamauth)
[![Code Climate](https://img.shields.io/codeclimate/github/maen-bn/openamauth.svg)](https://codeclimate.com/github/maen-bn/openamauth)
[![Code Climate](https://img.shields.io/codeclimate/issues/github/maen-bn/openamauth.svg)](https://codeclimate.com/github/maen-bn/openamauth)
[![Packagist](https://img.shields.io/packagist/v/maenbn/openamauth.svg)](https://packagist.org/packages/maenbn/openamauth)
[![Packagist](https://img.shields.io/packagist/dt/maenbn/openamauth.svg)](https://packagist.org/packages/maenbn/openamauth)

This is a PHP library for authenticating users via OpenAM

## NOTE: Usage with Laravel

This package has been changed to be framework agnostic. If you're using **Laravel 5.1** then continue to use **v1.1.*** 
 of this package. For later versions of Laravel, a new separate package for Laravel will be created using this package
 as it's core logic. More information will follow.


## Installation

The tool requires you have [PHP](https://php.net) 5.4.*+ (note 5.4 is not supported unless you are using a RHEL7 distro) 
and [Composer](https://getcomposer.org).

You will also need a [OpenAM](http://openam.forgerock.org/) server.

To install the package run the following composer command 

``` bash
composer require maenbn/openamauth
```

## Usage
### Setup

To setup an OpenAm object you will need to initialise a config object and pass that to the OpenAm factory:
 
 ```php
 // Construct parameters are address of your OpenAm server, deploy URI (optional), realm (optional)
 $config = new \Maenbn\OpenAmAuth\Config('https://myopenam.com', 'people', 'openam');
 // OpenAm instance
 $openAm = \Maenbn\OpenAmAuth\Factories\OpenAmFactory::create($config);
 ```
 
 ### Authenticating and retrieving the return token ID
 ```php
 if($openAm->authenticate('username', 'password'){
    $tokenId = $openAm->getTokenId();
    // Further successful authenication logic ...
 }
 ```
 
### Validate Token
If an OpenAm instance has ran a successful ```authenticate``` during the current runtime then you can validate
the token id without having to set a token ID on the instance. Other wise use the ```setTokenId``` setter before 
validating.

```php
// Returns a bool or throws and exception if not token ID is set
$valid = $openAm->setToken($tokenId)->validateTokenId();
```

### Getting users details 

If an OpenAm instance has ran a successful ```authenticate``` during the current runtime then you can get the users
 details return from OpenAM without having to set a token ID and the user ID. Other wise use the ```setTokenId``` 
 and ```setUid``` setters before running the ```setUser``` then ```getUser```.
  
 ```php
// Returns a stdClass with the user's details or null if the token ID and user ID have not been set
   $user = $openAm->setToken($tokenId)->setUid($uid)->setUser()->getUser();
   
// If successful authenicate has been ran
 if($openAm->authenticate('username', 'password'){
    $user = $openAm->getUser();
    // Further successful authenication logic ...
}
```

### Logging out
If an OpenAm instance has ran a successful ```authenticate``` during the current runtime then you can logout without 
having to set a token ID. Other wise use the ```setTokenId``` setter before logging out a user.

```php
// Return a bool based of success of log out
$openAm->setTokenId($tokenId)->logout();
```

### Accessing Config object via OpenAm object
When the ```Config``` object is injected into the constructor of ```OpenAm``` it'll modify the ```cookieName``` and
```cookieSecure``` property on ```Config``` if they're set to ```null```. This is done by retrieving this information 
from the OpenAm server. Obviously you may want to retrieve this data in order to set a cookie on your app correctly. 
Therefore you can access the ```Config``` object via the ```OpenAm``` object via the getter:

 ```php
 // Will return Config object
$config = $openAm->getConfig();

$cookieName = $config->getCookieName();
$cookieSecure = $cofig->getCookieSecure();
```