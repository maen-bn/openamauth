<?php

namespace Maenbn\OpenAmAuth;

use Maenbn\OpenAmAuth\Contracts\Curl;

class OpenAm
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

    public function __construct(Config $config, Curl $curl)
    {
        $this->config = $config;
        $this->curl = $curl;
        $this->setConfigCookieName();
    }

    /**
     * @return $this
     */
    protected function setConfigCookieName()
    {
        if(is_null($this->config->cookieName)){
            $this->config->cookieName = $this->setCurlHeadersAndOptions()
                ->setUrl($this->config->getUrl() . '/serverinfo/*')
                ->get()->cookieName;
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
        $url = $this->config->getUrl(true) . '/authenticate';
        $response = $this->setCurlHeadersAndOptions()->setUrl($url)->post($credentials);
        if(isset($response->tokenId)){
            $tokenValid= $this->setTokenId($response->tokenId)->validateTokenId();
            $this->setUser();
            return $tokenValid;
        }
        return false;
    }

    /**
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
     * @return $this
     */
    public function setUser()
    {
        if (is_null($this->getTokenId()) || is_null($this->getUid())) {
            return $this;
        }

        $url = $this->config->getUrl(true) . '/users/' . $this->getUid();
        $header = $this->config->cookieName . ':' . $this->getTokenId();
        $this->user = $this->setCurlHeadersAndOptions()->setUrl($url)->appendToHeaders([$header])->get();

        return $this;
    }

}