<?php

namespace Maenbn\OpenAmAuth\Factories;

use Maenbn\OpenAmAuth\Contracts\Strategies\Format;
use Maenbn\OpenAmAuth\Strategies\JsonToObject;

class StrategiesFactory
{
    /**
     * @return Format
     */
    public function newJsonToObject()
    {
        return new JsonToObject();
    }
}