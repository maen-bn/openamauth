<?php

/**
 * @covers \Maenbn\OpenAmAuth\Factories\CurlFactory
 */
class CurlFactoryUnitTest extends TestCase
{
    public function testFactoryReturnType()
    {
        $curlFactory = new Maenbn\OpenAmAuth\Factories\CurlFactory();
        $this->assertInstanceOf(Maenbn\OpenAmAuth\Contracts\Curl::class, $curlFactory->newCurl());
        $this->assertTrue(is_object($curlFactory->newCurl()));
    }
}