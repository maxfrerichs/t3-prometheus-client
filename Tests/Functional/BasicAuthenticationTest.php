<?php

use MFR\T3PromClient\Authentication\BasicAuthentication;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Http\ServerRequest;
use TYPO3\CMS\Core\Http\Uri;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

class BasicAuthenticationTest extends FunctionalTestCase
{
    protected BasicAuthentication $subject;

    public function setUp(): void
    {
        parent::setUp();
        $this->subject = new BasicAuthentication();
    }

    public function testValidBasicAuthentication(): void
    {
        $uri = new Uri('http://localhost/');
        $headers = [
            'authorization' => 'Basic dGVzdDpwYXNzd29yZA=='
        ];
        $server = [
            'SERVER_PORT' => 80
        ];
        $server['server'] = true;
        $request = new ServerRequest($uri, 'GET', 'php://memory', $headers, $server);
        $extConf = new ExtensionConfiguration();
        $extConf->set('t3_prometheus_client', [
            'basicAuth' => [
                'username' => 'test',
                'password' => 'password',
            ],
            'debug' => '0',
            'mode' => 'basic',
            'path' => '/metrics',
            'port' => '80',
            'token' => '',
        ]);
        self::assertTrue($this->subject->authenticate($extConf, $request));
    }


    public function testInvalidBasicAuthentication(): void
    {
        $uri = new Uri('http://localhost/');
        $headers = [
            'authorization' => 'Basic dGVzdDpwYXNzd29yZHM='
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
                'username' => 'test',
                'password' => 'password',
            ],
            'debug' => '0',
            'mode' => 'basic',
            'path' => '/metrics',
            'port' => '80',
            'token' => '',
        ]);
        self::assertFalse($this->subject->authenticate($extConf, $request));
    }
}