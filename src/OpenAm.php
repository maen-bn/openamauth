<?php

namespace Maenbn\OpenAmAuth;

use Maenbn\OpenAmAuth\Contracts\Curl as CurlContract;
use Maenbn\OpenAmAuth\Contracts\Config AS ConfigContract;
use Maenbn\OpenAmAuth\Contracts\OpenAm as OpenAmContract;

class OpenAm implements OpenAmContract
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var Curl
     */
    protected $curl;

    /**
     * @var string
     */
    protected $tokenId;

    /**
     * @var string
     */
    protected $uid;

    /**
     * @var \stdClass
     */
    protected $user;

    public function __construct(ConfigContract $config, CurlContract $curl)
    {
        $this->config = $config;
        $this->curl = $curl;
        $this->setConfigCookieData();
    }

    /**
     * @return $this
     */
    protected function setConfigCookieData()
    {
        if(is_null($this->config->getCookieName()) || is_null($this->config->getCookieName())){
            $serverInfo = $this->setCurlHeadersAndOptions()->setUrl($this->config->getUrl() . '/serverinfo/*')->get();
            if(is_null($this->config->getCookieName())){
                $this->config->setCookieName($serverInfo->cookieName);
            }
            if(is_null($serverInfo->secureCookie)){
                $this->config->setSecureCookie(false);
            }
        }
        return $this;
    }

    /**
     * @return Curl
     */
    protected function setCurlHeadersAndOptions()
    {
        $this->curl->setHeaders(['Content-Type: application/json'])
            ->setOptions([CURLOPT_RETURNTRANSFER => true, CURLOPT_HEADER => false]);
        return $this->curl;
    }

    /**
     * @return Config
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @return string
     */
    public function getTokenId()
    {
        return $this->tokenId;
    }

    /**
     * @param string $tokenId
     * @return $this
     */
    public function setTokenId($tokenId)
    {
        $this->tokenId = $tokenId;
        return $this;
    }

    /**
     * @return string
     */
    public function getUid()
    {
        return $this->uid;
    }

    /**
     * @param string $uid
     * @return $this
     */
    public function setUid($uid)
    {
        $this->uid = $uid;
        return $this;
    }

    /**
     * @param $username
     * @param $password
     * @return bool
     */
    public function authenticate($username, $password)
    {
        $credentials = ['X-OpenAM-Username: ' . $username, 'X-OpenAM-Password: ' . $password];
        $url = $this->config->setUrlWithRealm(true)->getUrl() . '/authenticate';
        $response = $this->setCurlHeadersAndOptions()->setUrl($url)->post($credentials);
        if(isset($response->tokenId)){
            $tokenValid= $this->setTokenId($response->tokenId)->validateTokenId();
            $this->setUser();
            return $tokenValid;
        }
        return false;
    }

    /**
     * Validate a user's session. Requires tokenId to be set. Can be done via setTokenId method
     *
     * @return bool
     * @throws \Exception
     */
    public function validateTokenId()
    {
        if(is_null($this->getTokenId())){
            throw new \Exception('A tokenId has not been set');
        }

        $baseResponse = new \stdClass();
        $baseResponse->valid = false;
        $baseResponse->uid = null;

        $url = $this->config->getUrl() . '/sessions/' . $this->getTokenId() . '?_action=validate';
        $response = $this->setCurlHeadersAndOptions()->setUrl($url)->post();
        $response = (object) array_merge((array) $baseResponse, (array) $response);

        $this->setUid($response->uid);

        return $response->valid;
    }

    /**
     * @return \stdClass
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Obtain an authenticated user's details. Make sure to set a tokenId and uid via the
     * setTokenId and setUid methods respectively
     *
     * @return $this
     */
    public function setUser()
    {
        if (is_null($this->getTokenId()) || is_null($this->getUid())) {
            return $this;
        }

        $url = $this->config->setUrlWithRealm(true)->getUrl() . '/users/' . $this->getUid();
        $header = $this->config->getCookieName() . ':' . $this->getTokenId();
        $this->user = $this->setCurlHeadersAndOptions()->setUrl($url)->appendToHeaders([$header])->get();

        return $this;
    }

    /**
     * Logout authenticated user. Make sure to set a tokenId via setTokenId method
     *
     * @return bool
     */
    public function logout()
    {
        $url = $this->config->setUrlWithRealm(true)->getUrl() . '/sessions/?_action=logout';
        $header = $this->config->getCookieName()  . ':' . $this->getTokenId();
        $response = $this->setCurlHeadersAndOptions()->setUrl($url)->appendToHeaders([$header])->post();

        if(isset($response->result) && $response->result == 'Successfully logged out'){
            $this->setTokenId(null);
            $this->setUid(null);
            $this->user = null;
            return true;
        }

        return false;
    }

}