<?php

namespace Maenbn\OpenAmAuth\Strategies;

use Maenbn\OpenAmAuth\Contracts\Strategies\Format;

class JsonToObject implements Format
{
    /**
     * @param string $json
     * @return object
     * @throws \Exception
     */
    public function format($json)
    {
        $object = json_decode($json);
        if(!is_object($object)){
            throw new \Exception('Formatting cannot create object');
        }
        return $object;
    }
}