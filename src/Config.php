<?php

namespace Maenbn\OpenAmAuth;

use Maenbn\OpenAmAuth\Contracts\Config as ConfigContract;

class Config implements ConfigContract
{

    /**
     * @var string
     */
    protected $baseUrl;

    /**
     * @var string|null
     */
    protected $realm;

    /**
     * @var string
     */
    protected $cookieName;

    /**
     * @var bool
     */
    protected $secureCookie;

    /**
     * Config constructor.
     * @param string $domainName
     * @param string $uri
     * @param string|null $realm
     */
    public function __construct($domainName, $realm = null, $uri = 'openam')
    {
        $this->setBaseUrl($domainName, $uri)->setRealm($realm);
    }

    /**
     * @param $domainName
     * @param $uri
     * @return $this
     */
    protected function setBaseUrl($domainName, $uri)
    {
        $this->baseUrl = $domainName . '/' . $uri . '/json';
        return $this;
    }

    /**
     * @param $realm
     * @return $this
     */
    protected function setRealm($realm)
    {
        $this->realm = $realm;
        return $this;
    }

    /**
     * @param bool $withRealm
     * @return string
     */
    public function getUrl($withRealm = false)
    {
        $url = $this->baseUrl;
        if($withRealm && !is_null($this->realm)){
            $url .= '/' . $this->realm;
        }
        return $url;
    }

    /**
     * @return null|string
     */
    public function getCookieName()
    {
        return $this->cookieName;
    }

    /**
     * @param string $cookieName
     * @return $this
     */
    public function setCookieName($cookieName)
    {
        $this->cookieName = $cookieName;
        return $this;
    }

    /**
     * @return null|bool
     */
    public function getSecureCookie()
    {
        return $this->secureCookie;
    }

    /**
     * @param string $secureCookie
     * @return $this
     */
    public function setSecureCookie($secureCookie)
    {
        $this->secureCookie = $secureCookie;
        return $this;
    }
}