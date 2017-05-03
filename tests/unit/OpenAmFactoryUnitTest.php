<?php

/**
 * @covers \Maenbn\OpenAmAuth\Factories\OpenAmFactory
 */
class OpenAmFactoryUnitTest extends TestCase
{
    public function testFactoryReturnType()
    {
        $config = new \Maenbn\OpenAmAuth\Config('https://myopenam.com', 'openam', 'people', 'iPlanetDirectoryPro');
        $openAm = \Maenbn\OpenAmAuth\Factories\OpenAmFactory::create($config);
        $this->assertInstanceOf(\Maenbn\OpenAmAuth\OpenAm::class, $openAm);
    }
}