<?php

use MFR\T3PromClient\Authentication\TokenAuthentication;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Http\ServerRequest;
use TYPO3\CMS\Core\Http\Uri;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

class TokenAuthenticationTest extends FunctionalTestCase
{
    protected TokenAuthentication $subject;

    public function setUp(): void
    {
        parent::setUp();
        $this->subject = new TokenAuthentication();
    }

    public function testValidTokenAuthentication(): void
    {
        $uri = new Uri('http://localhost/');
        $headers = [
            'authorization' => 'Bearer EGM0dp4QCVomiuA40kJcT0hu6Fv6Q7ckoJZ7Btq66ko1AKHJKcqVtscxhJXZsqJ3'
        ];
        $server = [
            'SERVER_PORT' => 80
        ];
        $server['server'] = true;
        $request = new ServerRequest($uri, 'GET', 'php://memory', $headers, $server);
        $extConf = new ExtensionConfiguration();
        $extConf->set('t3_prometheus_client', [
            'basicAuth' => [
                'password' => '',
                'username' => '',
            ],
            'debug' => '0',
            'mode' => 'token',
            'path' => '/metrics',
            'port' => '80',
            'token' => 'EGM0dp4QCVomiuA40kJcT0hu6Fv6Q7ckoJZ7Btq66ko1AKHJKcqVtscxhJXZsqJ3',
        ]);
        self::assertTrue($this->subject->authenticate($extConf, $request));
    }


    public function testInvalidTokenAuthentication(): void
    {
        $uri = new Uri('http://localhost/');
        $headers = [
            'authorization' => 'Bearer EGM0dp4QCVomiuA40kJcT0hu6Fv6Q7ckoJZ7Btq66ko1AKHJKcqVtscxhJXZsqJ3'
        ];
        $server = [
            'SERVER_PORT' => 80
        ];
        $server['server'] = true;
        $request = new ServerRequest($uri, 'GET', 'php://memory', $headers, $server);
        $extConf = new ExtensionConfiguration();
        $this->setUpBeforeClass();
        $extConf->set('t3_prometheus_client', [
            'basicAuth' => [
                'password' => '',
                'username' => '',
            ],
            'debug' => '0',
            'mode' => 'token',
            'path' => '/metrics',
            'port' => '80',
            'token' => 'EGM0dp4QCVomiuA40kJcT0hu6Fv6Q7ckoJZ7Btq66sko1AKHJKcqVtscxhJXZsqJ3',
        ]);
        self::assertFalse($this->subject->authenticate($extConf, $request));
    }
}