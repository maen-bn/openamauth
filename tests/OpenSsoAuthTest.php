<?php namespace Maenbn\Tests\OpenAmAuth;

/**
 * @runTestsInSeparateProcesses
 */
class OpenSsoAuthTest extends \Orchestra\Testbench\TestCase
{
    protected $config;

    protected $testUser;

    protected $testUserPassword;

    protected $tokenId;

    protected function getEnvironmentSetUp($app)
    {
        $config = $this->mockOpenSsoConfig();

        $this->config = $config;

        $app['config']->set('openam.serverAddress', $config['serverAddress']);
        $app['config']->set('openam.realm', $config['realm']);
        $app['config']->set('openam.cookiePath', $config['cookiePath']);
        $app['config']->set('openam.cookieDomain', $config['cookieDomain']);
        $app['config']->set('openam.cookieName', $config['cookieName']);
        $app['config']->set('openam.legacy', $config['legacy']);
        $app['config']->set('auth.driver', 'openam');

        $this->testUser = $config['testUser'];
        $this->testUserPassword = $config['testUserPassword'];
    }

    protected function mockOpenSsoConfig()
    {
        $configFile = dirname(__FILE__) . '/configOpenSso.php';

        if (!file_exists($configFile)) {
            $this->fail('Please take the configOpenSso.php.dist and create a configOpenSso.php file ' .
                'with your OpenSSO server details within the same directory');
        }

        return include $configFile;
    }

    protected function getPackageProviders($app)
    {
        return [
            'Maenbn\OpenAmAuth\OpenAmAuthServiceProvider',
        ];
    }

    public function testAuthentication()
    {

        //
        $this->assertTrue(\Auth::attempt(['username' => $this->testUser, 'password' => $this->testUserPassword]));
        $this->assertTrue(\Auth::attempt());
    }

    public function testAuthenticatedUser()
    {
        \Auth::attempt(['username' => $this->testUser, 'password' => $this->testUserPassword]);

        $user = \Auth::user();

        $this->assertObjectHasAttribute('tokenId', $user);
    }
}
