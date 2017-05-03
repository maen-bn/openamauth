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
     * @param string $cookieName
     * @return $this
     */
    public function setCookieName($cookieName);

    /**
     * @return null|bool
     */
    public function getSecureCookie();

    /**
     * @param string $cookieSecure
     * @return $this
     */
    public function setSecureCookie($cookieSecure);
}