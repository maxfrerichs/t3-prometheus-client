<?php
namespace MFR\Typo3Prometheus\Service;
use TYPO3\CMS\Reports\Registry\StatusRegistry;
use Prometheus\CollectorRegistry;
use Prometheus\RenderTextFormat;
use Prometheus\Storage\InMemory;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Localization\LanguageServiceFactory;

class MetricsService
{
    public function __construct(
        private readonly StatusRegistry $statusRegistry,
    ){}

    public function generate(): string
    {
        $statusProviders = $this->statusRegistry->getProviders();
        $collectorRegistry = new CollectorRegistry(new InMemory());
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
        return $result;
    }
}