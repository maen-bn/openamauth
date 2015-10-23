<?php namespace Maenbn\OpenAmAuth;

use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;;

interface UserInterface extends AuthenticatableContract
{
    public function setAttributes($attributes);
}
