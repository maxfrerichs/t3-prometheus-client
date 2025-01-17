<?php

namespace MFR\T3PromClient\Middleware;

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
    public function __construct(
        private readonly ResponseFactoryInterface $responseFactory,
        private readonly StreamFactoryInterface $streamFactory,
        private readonly ExtensionConfiguration $extensionConfiguration,
        private readonly PrometheusService $prometheusService
    ) {
    }

    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
    ): ResponseInterface {
        $requestPort = $request->getServerParams()['SERVER_PORT'];
        $metricsPort = $this->extensionConfiguration->get('typo3_prometheus', 'metricsPort') ?? 9090;
        $metricsPath = $this->extensionConfiguration->get('typo3_prometheus', 'metricsPath');

        if ($requestPort != $metricsPort) {
            return $this->responseFactory->createResponse(403);
        }
        if (($request->getRequestTarget() != $metricsPath)) {
            return $handler->handle($request);
        }

        $result = $this->prometheusService->renderMetrics();
        echo $result;
        return $this->responseFactory->createResponse(200)->withHeader('Content-Type', RenderTextFormat::MIME_TYPE);
    }
}
