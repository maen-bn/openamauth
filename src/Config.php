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
     * @var string
     */
    protected $realm;

    /**
     * @var string
     */
    protected $cookieName;

    /**
     * Config constructor.
     * @param $domainName
     * @param $uri
     * @param $realm
     * @param null $cookieName
     */
    public function __construct($domainName, $uri = 'openam', $realm = null, $cookieName = null)
    {
        $this->setBaseUrl($domainName, $uri)->setRealm($realm);
        $this->setCookieName($cookieName);
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
     * @param $cookieName
     * @return $this
     */
    public function setCookieName($cookieName)
    {
        $this->cookieName = $cookieName;
        return $this;
    }
}