<?php namespace Maenbn\OpenAmAuth;

use Illuminate\Contracts\Auth\Authenticatable as User;

interface UserInterface extends User {

    public function setAttributes($attributes);

}