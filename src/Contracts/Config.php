<?php

namespace Maenbn\OpenAmAuth\Contracts;

interface Config
{
    /**
     * @return string
     */
    public function getUrl();

    /**
     * @return bool
     */
    public function getUrlWithRealm();

    /**
     * @param bool $urlWithRealm
     * @return $this
     */
    public function setUrlWithRealm($urlWithRealm);

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