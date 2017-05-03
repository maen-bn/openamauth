<?php

/**
 * @covers \Maenbn\OpenAmAuth\Factories\StrategiesFactory
 */
class StrategiesFactoryUnitTest extends TestCase
{
    public function testFactoryReturnTypeForJsonToObject()
    {
        $factory = new \Maenbn\OpenAmAuth\Factories\StrategiesFactory();
        $this->assertInstanceOf('Maenbn\OpenAmAuth\Contracts\Strategies\Format', $factory->newJsonToObject());
    }
}