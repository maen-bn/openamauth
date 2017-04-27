<?php

/**
 * @covers \Maenbn\OpenAmAuth\Strategies\JsonToObject
 */
class JsonToObjectTest extends TestCase
{

    public function testReturnTypeIsObject()
    {
        $stringToJson = new Maenbn\OpenAmAuth\Strategies\JsonToObject();
        $object = $stringToJson->format('{"a":1,"b":2,"c":3,"d":4,"e":5}');
        $this->assertTrue(is_object($object));
    }

    /**
     * @expectedException Exception
     * @expectedExceptionMessage Formatting cannot create object
     */
    public function testExceptionThrown()
    {
        $stringToJson = new Maenbn\OpenAmAuth\Strategies\JsonToObject();
        $object = $stringToJson->format(null);
        $this->assertTrue(is_object($object));
    }

}