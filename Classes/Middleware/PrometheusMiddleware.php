<?php
namespace MFR\Typo3Prometheus\Middleware;

use MFR\Typo3Prometheus\Service\PrometheusService;
use Prometheus\RenderTextFormat;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
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
        $metricsPort = $this->extensionConfiguration->get('typo3_prometheus', 'metricsPort');
        $metricsPath = $this->extensionConfiguration->get('typo3_prometheus', 'metricsPath');
 
        if ($request->getRequestTarget() == $metricsPath && $requestPort != 80) {
            // fuck off everyone that tries to access metrics from outside 
            if ($metricsPort && $requestPort != $metricsPort) {
                return $this->responseFactory->createResponse(403);
            }
            $result = $this->prometheusService->generate();
            echo $result;
            return $this->responseFactory->createResponse(200)->withHeader('Content-Type', RenderTextFormat::MIME_TYPE);
        } else {
            return $handler->handle($request);
        }
    }
}
