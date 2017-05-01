<?php

namespace Maenbn\OpenAmAuth\Factories;

use Maenbn\OpenAmAuth\Curl;

class CurlFactory
{
    /**
     * @return Curl
     */
    public function newCurl()
    {
        return new Curl();
    }
}