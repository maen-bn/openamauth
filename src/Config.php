<?php

namespace Maenbn\OpenAmAuth;


class Config
{

    /**
     * @var string
     */
    public $baseUrl;

    /**
     * @var string
     */
    public $realm;

    /**
     * @var string
     */
    public $cookieName;

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
        $this->cookieName = $cookieName;
    }

    protected function setBaseUrl($domainName, $uri)
    {
        $this->baseUrl = $domainName . '/' . $uri . '/json';
        return $this;
    }

    protected function setRealm($realm)
    {
        $this->realm = $realm;
        return $this;
    }

    public function getUrl($withRealm = false)
    {
        $url = $this->baseUrl;
        if($withRealm && !is_null($this->realm)){
            $url .= '/' . $this->realm;
        }
        return $url;
    }
}