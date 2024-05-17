<?php
namespace MFR\Typo3Prometheus\Middleware;

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
            $statusRegistry = GeneralUtility::makeInstance(StatusRegistry::class);
            $statusProviders = $statusRegistry->getProviders();
            $collectorRegistry = new CollectorRegistry(new InMemory());
            // fuck off everyone that tries to access metrics from outside 
            if ($metricsPort && $requestPort != $metricsPort) {
                return $this->responseFactory->createResponse(403);
            }
            /**
             * Some status reports use LanguageService defined in this global variable to translate labels, but it's not initiated at the time
             * middleware is executed. While it's discouraged to instantiate on your own, I think it's okay in this case 
             * because not much other TYPO3-related stuff is to be executed afterwards
             */
            $GLOBALS['LANG'] = GeneralUtility::makeInstance(LanguageServiceFactory::class)->createFromUserPreferences($GLOBALS['BE_USER']);
            foreach ($statusProviders as $statusProviderItem) {
                $status = $statusProviderItem->getStatus();
                foreach ($status as $index => $statusItem) {
                    $metricName = strtolower(preg_replace("/[ .\/,-]/", "", $statusItem->getTitle())) . (string) $index;
                    $gauge = $collectorRegistry->registerGauge("typo3", $metricName, "severity", ['message']);
                    $gauge->set((float) $statusItem->getSeverity()->value, [strip_tags($statusItem->getMessage())]);
                }
            }
            $renderer = new RenderTextFormat();
            $result = $renderer->render($collectorRegistry->getMetricFamilySamples());
            echo $result;
            return $this->responseFactory->createResponse(200)->withHeader('Content-Type', RenderTextFormat::MIME_TYPE);
        } else {
            return $handler->handle($request);
        }
    }
}
