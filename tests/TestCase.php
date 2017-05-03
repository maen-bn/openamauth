<?php

class TestCase extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \Maenbn\OpenAmAuth\Curl
     */
    public $curl;

    public function setUp()
    {
        parent::setUp();
        $this->mockCurl();
    }

    public function mockCurl($executeReturn = 'Hello')
    {
        $this->curl = $this->getMockBuilder('Maenbn\OpenAmAuth\Curl')
            ->setMethods(array('execute'))
            ->getMock();
        $this->curl->method('execute')->willReturn($executeReturn);
    }
}