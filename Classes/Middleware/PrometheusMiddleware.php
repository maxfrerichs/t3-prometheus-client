<?php
namespace MFR\Typo3Prometheus\Middleware;

use MFR\Typo3Prometheus\Service\MetricsService;
use Prometheus\RenderTextFormat;
use Prometheus\Storage\InMemory;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Localization\LanguageServiceFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Reports\Registry\StatusRegistry;
use Prometheus\CollectorRegistry;
use TYPO3\CMS\Reports\StatusProviderInterface;

class PrometheusMiddleware implements MiddlewareInterface
{
    public function __construct(
        private readonly ResponseFactoryInterface $responseFactory,
        private readonly StreamFactoryInterface $streamFactory,
        private readonly ExtensionConfiguration $extensionConfiguration
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
            $metricService = GeneralUtility::makeInstance(MetricsService::class);
            $result = $metricService->generate();
            echo $result;
            return $this->responseFactory->createResponse(200)->withHeader('Content-Type', RenderTextFormat::MIME_TYPE);
        } else {
            return $handler->handle($request);
        }
    }
}
