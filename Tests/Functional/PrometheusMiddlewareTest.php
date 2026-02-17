<?php

declare(strict_types=1);

use MFR\T3PromClient\Authentication\AuthenticationFactory;
use MFR\T3PromClient\Middleware\PrometheusMiddleware;
use MFR\T3PromClient\Service\PrometheusService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Http\Response;
use TYPO3\CMS\Core\Http\ServerRequest;
use TYPO3\CMS\Core\Http\Stream;
use TYPO3\CMS\Core\Http\Uri;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

/**
 * Tests middleware for unwanted interference with frontend rendering
 */
class PrometheusMiddlewareTest extends FunctionalTestCase
{
    protected array $testExtensionsToLoad = [
        't3_prometheus_client',
    ];

    protected ExtensionConfiguration $extConf;

    private function createTestHandler(ResponseInterface $response): RequestHandlerInterface
    {
        return new class($response) implements RequestHandlerInterface {
            public function __construct(
                private readonly ResponseInterface $expectedResponse
            ){}

            public function handle(ServerRequestInterface $request): ResponseInterface
            {
                return $this->expectedResponse;
            }
        };
    }

    public function setUp(): void
    {
        parent::setUp();
        $this->extConf = new ExtensionConfiguration();
    }

    public function testFrontendPageReturns200WithBasicAuthenticationActivated(): void
    {
        $this->extConf->set('t3_prometheus_client', [
            'basicAuth' => [
                'username' => 'admin',
                'password' => 'secure123',
            ],
            'debug' => '0',
            'mode' => 'basic',
            'path' => '/metrics',
            'port' => '80',
            'token' => '',
        ]);

        $uri = new Uri('http://localhost/');
        $headers = [];
        $server = [
            'SERVER_PORT' => 80,
        ];
        $request = new ServerRequest($uri, 'GET', 'php://memory', $headers, $server);

        $frontendResponse = new Response('php://temp', 200);
        $handler = $this->createTestHandler($frontendResponse);
        $responseFactory = new \TYPO3\CMS\Core\Http\ResponseFactory();
        $streamFactory = new \TYPO3\CMS\Core\Http\StreamFactory();
        $promService = $this->createMock(PrometheusService::class);
        $authFactory = new AuthenticationFactory();
        $middleware = new PrometheusMiddleware(
            $responseFactory,
            $streamFactory,
            $this->extConf,
            $promService,
            $authFactory
        );

        $response = $middleware->process($request, $handler);
        self::assertEquals(200, $response->getStatusCode());
    }

    public function testFrontendPageReturns200WithTokenAuthenticationActivated(): void
    {
        $this->extConf->set('t3_prometheus_client', [
            'basicAuth' => [
                'username' => '',
                'password' => '',
            ],
            'debug' => '0',
            'mode' => 'token',
            'path' => '/metrics',
            'port' => '80',
            'token' => 'my-secret-token-12345',
        ]);

        $uri = new Uri('http://localhost/');
        $headers = [];
        $server = [
            'SERVER_PORT' => 80,
        ];
        $request = new ServerRequest($uri, 'GET', 'php://memory', $headers, $server);

        // Mock handler returns a successful frontend response
        $body = new Stream('php://temp', 'rw');
        $body->write('<html><body>Page Content</body></html>');
        $frontendResponse = new Response($body, 200);

        $handler = $this->createTestHandler($frontendResponse);

        // Create the middleware with real factories
        $responseFactory = new \TYPO3\CMS\Core\Http\ResponseFactory();
        $streamFactory = new \TYPO3\CMS\Core\Http\StreamFactory();
        $promService = $this->createMock(PrometheusService::class);
        $authFactory = new AuthenticationFactory();

        $middleware = new PrometheusMiddleware(
            $responseFactory,
            $streamFactory,
            $this->extConf,
            $promService,
            $authFactory
        );

        $response = $middleware->process($request, $handler);
        self::assertEquals(200, $response->getStatusCode());
    }


    public function testFrontendPageReturns200EvenWithWrongPort(): void
    {
        $this->extConf->set('t3_prometheus_client', [
            'basicAuth' => [
                'username' => 'admin',
                'password' => 'password',
            ],
            'debug' => '0',
            'mode' => 'basic',
            'path' => '/metrics',
            'port' => '8080',
            'token' => '',
        ]);

        $uri = new Uri('http://localhost/');
        $headers = [];
        $server = [
            'SERVER_PORT' => 80,
        ];
        $request = new ServerRequest($uri, 'GET', 'php://memory', $headers, $server);

        // Mock handler
        $body = new Stream('php://temp', 'rw');
        $body->write('<html><body>Welcome</body></html>');
        $frontendResponse = new Response($body, 200);

        $handler = $this->createTestHandler($frontendResponse);

        $responseFactory = new \TYPO3\CMS\Core\Http\ResponseFactory();
        $streamFactory = new \TYPO3\CMS\Core\Http\StreamFactory();
        $promService = $this->createMock(PrometheusService::class);
        $authFactory = new AuthenticationFactory();

        $middleware = new PrometheusMiddleware(
            $responseFactory,
            $streamFactory,
            $this->extConf,
            $promService,
            $authFactory
        );

        $response = $middleware->process($request, $handler);
        self::assertEquals(200, $response->getStatusCode());
    }

    public function testMetricsEndpointReturns403WhenAuthenticationFails(): void
    {
        $this->extConf->set('t3_prometheus_client', [
            'basicAuth' => [
                'username' => 'admin',
                'password' => 'secret',
            ],
            'debug' => '0',
            'mode' => 'basic',
            'path' => '/metrics',
            'port' => '80',
            'token' => '',
        ]);

        $uri = new Uri('http://localhost/metrics');
        $headers = [];
        $server = [
            'SERVER_PORT' => 80,
        ];
        $request = new ServerRequest($uri, 'GET', 'php://memory', $headers, $server);
        $request = $request->withRequestTarget('/metrics');
        $handler = $this->createMock(RequestHandlerInterface::class);
        $handler->expects(self::never())->method('handle');
        $responseFactory = new \TYPO3\CMS\Core\Http\ResponseFactory();
        $streamFactory = new \TYPO3\CMS\Core\Http\StreamFactory();
        $promService = $this->createMock(PrometheusService::class);
        $authFactory = new AuthenticationFactory();

        $middleware = new PrometheusMiddleware(
            $responseFactory,
            $streamFactory,
            $this->extConf,
            $promService,
            $authFactory
        );

        $response = $middleware->process($request, $handler);
        self::assertEquals(403, $response->getStatusCode());
    }
}
