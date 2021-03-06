<?php

namespace Maenbn\OpenAmAuth\Contracts;

use Maenbn\OpenAmAuth\Contracts\Strategies\Format;

interface Curl
{
    /**
     * @return bool|resource
     */
    public function getSession();

    /**
     * @param $url
     * @return $this
     */
    public function setUrl($url);

    /**
     * @return string
     */
    public function getUrl();

    /**
     * @param array $headers
     * @return $this
     */
    public function setHeaders(array $headers);

    /**
     * @return array
     */
    public function getHeaders();

    /**
     * @param array $headers
     * @return $this
     */
    public function appendToHeaders(array $headers);

    /**
     * @param int $option
     * @param mixed $value
     * @return $this
     */
    public function setOption($option, $value);

    /**
     * @param array $options
     * @return $this
     */
    public function setOptions(array $options);

    /**
     * @return Format
     */
    public function getResultFormat();

    /**
     * @param mixed $resultFormat
     * @return $this
     */
    public function setResultFormat(Format $resultFormat);

    /**
     * @param array $data
     * @return mixed
     */
    public function get(array $data = []);

    /**
     * @param array $data
     * @return mixed
     */
    public function post(array $data = []);

    /**
     * @return mixed
     * @throws \Exception
     */
    public function execute();
}