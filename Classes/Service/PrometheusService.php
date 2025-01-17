<?php

namespace MFR\T3PromClient\Service;

use MFR\T3PromClient\Event\RegisterStatusProviderEvent;
use Prometheus\CollectorRegistry;
use Prometheus\RenderTextFormat;
use Prometheus\Storage\InMemory;
use Psr\EventDispatcher\EventDispatcherInterface;
use TYPO3\CMS\Core\Information\Typo3Version;
use TYPO3\CMS\Core\Localization\LanguageServiceFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Install\Report\EnvironmentStatusReport;
use TYPO3\CMS\Install\Report\InstallStatusReport;

class PrometheusService
{
    public function __construct(
        private EnvironmentStatusReport $environmentStatusReport,
        private InstallStatusReport $installStatusReport,
        private readonly EventDispatcherInterface $eventDispatcher,
        private Typo3Version $typo3Version
    ) {
    }

    public function renderMetrics(): string
    {
        $statusProviders = [
            $this->environmentStatusReport,
            $this->installStatusReport,
        ];

        $this->eventDispatcher->dispatch(new RegisterStatusProviderEvent($statusProviders));

        $collectorRegistry = new CollectorRegistry(new InMemory());
        $GLOBALS['LANG'] = GeneralUtility::makeInstance(LanguageServiceFactory::class)->createFromUserPreferences($GLOBALS['BE_USER']);
        foreach ($statusProviders as $statusProviderItem) {
            $status = $statusProviderItem->getStatus();
            foreach ($status as $index => $statusItem) {
                $metricName = strtolower(preg_replace("/[ .\/,-]/", '', $statusItem->getTitle())) . (string)$index;
                $gauge = $collectorRegistry->registerGauge('typo3', $metricName, 'severity', ['severity', 'message']);
                $severity = $statusItem->getSeverity()->value;
                $gauge->set((float)$severity, [$severity, strip_tags($statusItem->getMessage())]);
            }
        }
        $renderer = new RenderTextFormat();
        $result = $renderer->render($collectorRegistry->getMetricFamilySamples());
        return $result;
    }
}
