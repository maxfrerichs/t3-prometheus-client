<?php

namespace MFR\T3PromClient\Middleware;

use MFR\T3PromClient\Authentication\AuthenticationFactory;
use MFR\T3PromClient\Enum\RetrieveMode;
use MFR\T3PromClient\Service\PrometheusService;
use Prometheus\RenderTextFormat;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;

class PrometheusMiddleware implements MiddlewareInterface
{
    const EXT_KEY = 't3_prometheus_client';
    public function __construct(
        private readonly ResponseFactoryInterface $responseFactory,
        private readonly StreamFactoryInterface $streamFactory,
        private readonly ExtensionConfiguration $config,
        private readonly PrometheusService $promService,
        private readonly AuthenticationFactory $authFactory,
    ) {
    }

    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler,
    ): ResponseInterface {
        $authentication = $this->authFactory->getAuthentication(
            $this->config->get(self::EXT_KEY)['mode']
        );

        if (($request->getRequestTarget() != $this->config->get(self::EXT_KEY)['path'])) {
            return $handler->handle($request);
        }

        if (!$authentication->authenticate(config: $this->config, request: $request) && $this->config->get(self::EXT_KEY)['debug'] == false) {
            return $this->responseFactory->createResponse(403, 'Authorization failed.');
        }

        echo $this->promService->renderMetrics(RetrieveMode::SCRAPE, $this->config);
        return $this->responseFactory->createResponse(200)->withHeader('Content-Type', RenderTextFormat::MIME_TYPE);
    }
}
