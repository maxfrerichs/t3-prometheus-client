<?php
namespace MFR\Typo3Prometheus\Service;
use TYPO3\CMS\Install\Report\EnvironmentStatusReport;
use TYPO3\CMS\Install\Report\InstallStatusReport;
use Prometheus\CollectorRegistry;
use Prometheus\RenderTextFormat;
use Prometheus\Storage\InMemory;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Localization\LanguageServiceFactory;

class PrometheusService
{
    public function __construct(
        private EnvironmentStatusReport $environmentStatusReport,
        private InstallStatusReport $installStatusReport
    ){}

    public function generate(): string
    {
        $statusProviders = [
            $this->environmentStatusReport,
            $this->installStatusReport
        ];
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
        unset($this->statusRegistry);
        return $result;
    }
}