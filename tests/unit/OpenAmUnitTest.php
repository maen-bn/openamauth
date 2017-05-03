<?php

/**
 * @covers \Maenbn\OpenAmAuth\OpenAm
 */
class OpenAmUnitTest extends TestCase
{
    /**
     * @var \Maenbn\OpenAmAuth\OpenAm|PHPUnit_Framework_MockObject_MockObject
     */
    protected $openAm;

    /**
     * @var \Maenbn\OpenAmAuth\Config
     */
    protected $config;

    public function mockOpenAm($response, $mockOpenAm = true, $noCookieName = false)
    {
        $this->mockCurl($response);
        $cookieName = 'iPlanetDirectoryPro';
        $secureCookie = false;
        if($noCookieName){
            $cookieName = null;
            $secureCookie = null;
        }
        $this->config = new \Maenbn\OpenAmAuth\Config('https://myopenam.com', 'openam', 'people');
        $this->config->setCookieName($cookieName);
        $this->config->setSecureCookie($secureCookie);
        $strategiesFactory = new \Maenbn\OpenAmAuth\Factories\StrategiesFactory();
        $this->curl->setResultFormat($strategiesFactory->newJsonToObject());
        if($mockOpenAm) {
            $openAm = $this->getMockBuilder(Maenbn\OpenAmAuth\OpenAm::class)
                ->setConstructorArgs([$this->config, $this->curl]);
            $openAm->setMethods(['validateTokenId']);
            $this->openAm = $openAm->getMock();
        }
        else {
            $this->openAm = new \Maenbn\OpenAmAuth\OpenAm($this->config, $this->curl);
        }
    }

    public function testAuthenticate()
    {
        $mockedResponse = new stdClass();
        $mockedResponse->tokenId = 1123123;
        $this->mockOpenAm($mockedResponse);
        $this->openAm->method('validateTokenId')->willReturn(true);
        $this->assertTrue($this->openAm->authenticate('abc123', 'password'));
        $this->assertEquals(1123123, $this->openAm->getTokenId());
    }

    public function testFailedAuthentication()
    {
        $this->mockOpenAm('dsadas');
        $this->assertFalse($this->openAm->authenticate('abc123', 'password'));
        $this->assertEquals(null, $this->openAm->getTokenId());
    }

    public function testValidateTokenReturnsTrue()
    {
        $mockedResponse = new stdClass();
        $mockedResponse->valid = true;
        $mockedResponse->uid = 'abc123';
        $this->mockOpenAm($mockedResponse, false);
        $this->assertTrue($this->openAm->setTokenId('fasdfasdf')->validateTokenId());
        $this->assertEquals('abc123', $this->openAm->getUid());
    }

    public function testValidateTokenReturnsFalse()
    {
        $mockedResponse = new stdClass();
        $mockedResponse->valid = false;
        $this->mockOpenAm($mockedResponse, false);
        $this->assertFalse($this->openAm->setTokenId('fasdfasdf')->validateTokenId());
        $this->assertNull($this->openAm->getUid());
    }

    /**
     * @expectedException Exception
     * @expectedExceptionMessage A tokenId has not been set
     */
    public function testValidateThrowsException()
    {
        $mockedResponse = new stdClass();
        $mockedResponse->valid = false;
        $this->mockOpenAm($mockedResponse, false);
        $this->assertFalse($this->openAm->validateTokenId());
    }


    public function testSetUser()
    {
        $mockedResponse = new stdClass();
        $mockedResponse->username = 'abc123';
        $mockedResponse->realm = 'people';
        $mockedResponse->mail = ['abc123@test.com'];
        $this->mockOpenAm($mockedResponse, false);
        $this->openAm->setTokenId('12321432')->setUid('abc123')->setUser();
        $this->assertObjectHasAttribute('username',$this->openAm->getUser());
        $this->assertInstanceOf(stdClass::class, $this->openAm->getUser());
    }

    public function testUserIsSetToNullWhenTokenOrUidHasNotBeenSet()
    {
        $mockedResponse = new stdClass();
        $mockedResponse->username = 'abc123';
        $mockedResponse->realm = 'people';
        $mockedResponse->mail = ['abc123@test.com'];
        $this->mockOpenAm($mockedResponse, false);
        $this->openAm->setUser();
        $this->assertNull($this->openAm->getUser());
    }

    public function testCookieNameIsSetWhenNotSetOriginallyInConfig()
    {
        $mockedResponse = new stdClass();
        $mockedResponse->cookieName = 'iPlanetDirectoryPro';
        $mockedResponse->secureCookie = false;
        $this->mockOpenAm($mockedResponse, false, true);
        $this->assertEquals('iPlanetDirectoryPro', $this->config->getCookieName());
    }

    public function testLogout()
    {
        $mockedResponse = new stdClass();
        $mockedResponse->result = 'Successfully logged out';
        $this->mockOpenAm($mockedResponse, false);
        $result = $this->openAm->setTokenId('234124')->logout();
        $this->assertTrue($result);
        $this->assertNull($this->openAm->getTokenId());
        $this->assertNull($this->openAm->getUid());
        $this->assertNull($this->openAm->getUser());
    }

    public function testFailedLogout()
    {
        $mockedResponse = new stdClass();
        $mockedResponse->result = 'Failed';
        $this->mockOpenAm($mockedResponse, false);
        $result = $this->openAm->setTokenId('234124')->logout();
        $this->assertFalse($result);
    }

    public function testConfigGetter()
    {
        $mockedResponse = new stdClass();
        $mockedResponse->cookieName = 'iPlanetDirectoryPro';
        $mockedResponse->secureCookie = false;
        $this->mockOpenAm($mockedResponse, false, true);
        $this->assertEquals('iPlanetDirectoryPro', $this->openAm->getConfig()->getCookieName());
        $this->assertFalse($this->openAm->getConfig()->getSecureCookie());
    }
}