<?php

namespace Maenbn\OpenAmAuth;

use Maenbn\OpenAmAuth\Contracts\UserInterface;

class OpenAmUser implements UserInterface
{
    use Authenticatable;

    /**
     * Dynamically access the user's attributes.
     *
     * @param  string $key
     * @return mixed
     */
    public function __get($key)
    {
        return $this->$key;
    }
    /**
     * Dynamically set an attribute on the user.
     *
     * @param  string $key
     * @param  mixed $value
     * @return void
     */
    public function __set($key, $value)
    {
        $this->$key = $value;
    }
    /**
     * Dynamically check if a value is set on the user.
     *
     * @param  string $key
     * @return bool
     */
    public function __isset($key)
    {
        return isset($this->$key);
    }
    /**
     * Dynamically unset a value on the user.
     *
     * @param  string $key
     * @return bool
     */
    public function __unset($key)
    {
        unset($this->$key);
    }
}
