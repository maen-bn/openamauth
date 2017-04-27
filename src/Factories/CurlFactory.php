<?php

namespace Maenbn\OpenAmAuth\Factories;

class CurlFactory
{
    /**
     * @return \Maenbn\OpenAmAuth\Contracts\Curl
     */
    public function newCurl()
    {
        return new \Maenbn\OpenAmAuth\Curl();
    }
}