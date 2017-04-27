<?php

/**
 * @covers \Maenbn\OpenAmAuth\Curl
 */
class CurlIntegrationTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();
        $this->curl = new \Maenbn\OpenAmAuth\Curl();
        $this->curl->setHeaders([CURLOPT_HTTPHEADER => ['User-Agent:Mozilla/5.0 (X11; Linux x86_64)']])
            ->setOptions([CURLOPT_RETURNTRANSFER => true])
            ->setUrl('https://api.github.com/search/repositories?q=maen-bn/openamauth');
    }

    public function testUsingAWebService()
    {
        $result = $this->curl->setResultFormat(new \Maenbn\OpenAmAuth\Strategies\JsonToObject())->get();
        $this->assertTrue(is_object($result));
        $this->assertTrue(is_resource($this->curl->getSession()));
        $this->assertEmpty($this->curl->getHeaders());
        $this->assertEmpty($this->curl->getUrl());
    }

    public function testWithoutUsingAResultFormatter()
    {
        $result = $this->curl->get();
        $this->assertTrue(!is_object($result));
    }
}