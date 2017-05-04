<?php

/**
 * @covers \Maenbn\OpenAmAuth\Config
 */
class ConfigUnitTest extends TestCase
{

    public function testConfigProps()
    {
        $config = new \Maenbn\OpenAmAuth\Config('https://myopenam.com');
        $this->assertEquals('https://myopenam.com/openam/json', $config->getUrl());
    }

    public function testConfigPropsWithRealm()
    {
        $config = new \Maenbn\OpenAmAuth\Config('https://myopenam.com', 'people', 'openam');
        $this->assertEquals('https://myopenam.com/openam/json/people', $config->getUrl(true));
    }

    public function testCookieNameSetterAndGetter()
    {
        $config = new \Maenbn\OpenAmAuth\Config('https://myopenam.com');
        $returnedFromSetter = $config->setCookieName('iPlanetDirectoryPro');
        $this->assertInstanceOf(Maenbn\OpenAmAuth\Config::class, $returnedFromSetter);
        $this->assertEquals('iPlanetDirectoryPro', $config->getCookieName());
    }

    public function testCookieSecureSetterAndGetter()
    {
        $config = new \Maenbn\OpenAmAuth\Config('https://myopenam.com');
        $this->assertNull($config->getSecureCookie());
        $returnedFromSetter = $config->setSecureCookie(true);
        $this->assertInstanceOf(Maenbn\OpenAmAuth\Config::class, $returnedFromSetter);
        $this->assertTrue($config->getSecureCookie());
    }
}