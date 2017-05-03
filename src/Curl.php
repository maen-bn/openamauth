<?php

namespace Maenbn\OpenAmAuth;

use \Maenbn\OpenAmAuth\Contracts\Curl as CurlContract;
use Maenbn\OpenAmAuth\Contracts\Strategies\Format;

class Curl implements CurlContract
{
    /**
     * @var string
     */
    protected $url;

    /**
     * @var array
     */
    protected $headers = [];

    /**
     * Curl handle resource
     *
     * @var resource|bool
     */
    protected $session;

    /**
     * @var Format
     */
    protected $resultFormat;

    /**
     * Curl constructor.
     */
    public function __construct()
    {
        $this->setSession();
    }

    /**
     * @return $this
     */
    protected function setSession()
    {
        $this->session = curl_init();
        return $this;
    }

    /**
     * @return bool|resource
     */
    public function getSession()
    {
        return $this->session;
    }

    /**
     * @param $url
     * @return $this
     */
    public function setUrl($url)
    {
        $this->url = $url;
        return $this;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param array $headers
     * @return $this
     */
    public function setHeaders(array $headers)
    {
        $this->headers = $headers;
        return $this;
    }

    /**
     * @return array
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * @param array $headers
     * @return $this
     */
    public function appendToHeaders(array $headers)
    {
        $this->setHeaders(array_merge($this->getHeaders(), $headers));
        return $this;
    }

    /**
     * @param int $option
     * @param mixed $value
     * @return $this
     */
    public function setOption($option, $value)
    {
        curl_setopt($this->getSession(), $option, $value);
        return $this;
    }

    /**
     * @param array $options
     * @return $this
     */
    public function setOptions(array $options)
    {
        foreach ($options as $option => $value){
            $this->setOption($option, $value);
        }
        return $this;
    }

    /**
     * @return Format
     */
    public function getResultFormat()
    {
        return $this->resultFormat;
    }

    /**
     * @param mixed $resultFormat
     * @return $this
     */
    public function setResultFormat(Format $resultFormat)
    {
        $this->resultFormat = $resultFormat;
        return $this;
    }

    /**
     * @param array $data
     * @return mixed
     */
    public function get(array $data = [])
    {
        return $this->appendToHeaders($data)->execute();
    }

    /**
     * @param array $data
     * @return mixed
     */
    public function post(array $data = [])
    {
        return $this->appendToHeaders($data)->setOption(CURLOPT_CUSTOMREQUEST, "POST")->execute();
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    public function execute()
    {
        $url = $this->getUrl();

        if(empty($url)){
            throw new \Exception("A url must be set before curl can be executed");
        }
        $this->setOption(CURLOPT_URL, $url)->setOption(CURLOPT_HTTPHEADER, $this->getHeaders());

        $result = curl_exec($this->getSession());
        $this->close()->reset();
        return $this->format($result);
    }

    /**
     * @param $result
     * @return mixed
     */
    protected function format($result)
    {
        if(!is_null($this->getResultFormat())){
            return $this->getResultFormat()->format($result);
        }
        return $result;
    }

    /**
     * Close cURL session and run reset()
     *
     * @return $this
     */
    protected function close()
    {
        curl_close($this->session);
        return $this;
    }

    /**
     * Set a fresh session for reuse and clear all other properties
     *
     * @return void
     */
    private function reset()
    {
        $this->setSession()->setHeaders([])->setUrl('');
    }
}