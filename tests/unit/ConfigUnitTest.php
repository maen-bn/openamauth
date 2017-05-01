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

    public function testConfigPropsWithRealmAndCookieNameSet()
    {
        $config = new \Maenbn\OpenAmAuth\Config('https://myopenam.com', 'openam', 'people', 'iPlanetDirectoryPro');
        $this->assertEquals('https://myopenam.com/openam/json/people', $config->getUrl(true));
    }
}