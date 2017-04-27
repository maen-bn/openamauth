<?php

namespace Maenbn\OpenAmAuth\Contracts\Strategies;

interface Format
{
    /**
     * @param mixed $toFormat
     * @return mixed
     * @throws \Exception
     */
    public function format($toFormat);
}