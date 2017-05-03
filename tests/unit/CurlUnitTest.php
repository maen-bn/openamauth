<?php

/**
 * @covers \Maenbn\OpenAmAuth\Curl
 */
class CurlUnitTest extends TestCase
{

    public function testGet()
    {
        $url = 'https://test.com';
        $result = $this->curl->setUrl($url)->setHeaders([CURLOPT_RETURNTRANSFER => true])
            ->appendToHeaders([CURLOPT_HEADER, false])
            ->setOptions([CURLOPT_HTTPHEADER => ['Content-Type: application/json']])
            ->get();

        $this->assertTrue(is_string($result));
        $this->assertEquals('Hello', $result);
        $this->assertTrue(is_string($this->curl->getUrl()));
        $this->assertEquals($url, $this->curl->getUrl());
        $this->assertTrue(is_array($this->curl->getHeaders()));
        $this->assertEquals(current($this->curl->getHeaders()), true);
    }

    public function testPost()
    {
        $url = 'https://test.com/auth';
        $result = $this->curl->setUrl($url)->setHeaders([CURLOPT_RETURNTRANSFER => true])
            ->appendToHeaders([CURLOPT_HEADER, false])
            ->setOptions([CURLOPT_HTTPHEADER => ['Content-Type: application/json']])
            ->post(['username' => 'abc123']);
        $this->assertTrue(is_string($result));
        $this->assertEquals('Hello', $result);
    }

    /**
     * @expectedException Exception
     * @expectedExceptionMessage A url must be set before curl can be executed
     */
    public function testExceptionIsThrown()
    {
        $curl = new \Maenbn\OpenAmAuth\Curl();
        $curl->get();
    }
}