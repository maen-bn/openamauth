<?php

namespace Maenbn\OpenAmAuth\Contracts;

interface Config
{
    /**
     * @param bool $withRealm
     * @return string
     */
    public function getUrl($withRealm = false);

    /**
     * @return null|string
     */
    public function getCookieName();

    /**
     * @param $cookieName
     * @return $this
     */
    public function setCookieName($cookieName);
}