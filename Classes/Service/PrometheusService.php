<?php

namespace MFR\T3PromClient\Service;

use MFR\T3PromClient\Enum\MetricType;
use MFR\T3PromClient\Enum\RetrieveMode;
use MFR\T3PromClient\Event\BeforeMetricsRenderedEvent;
use MFR\T3PromClient\Exception\UnknownTypeException;
use MFR\T3PromClient\Metrics\MetricInterface;
use MFR\T3PromClient\Registry\MetricRegistry;
use Prometheus\CollectorRegistry;
use Prometheus\RenderTextFormat;
use Prometheus\Storage\InMemory;
use PrometheusPushGateway\PushGateway;
use Psr\EventDispatcher\EventDispatcherInterface;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;

class PrometheusService
{
    const EXT_KEY = 't3_prometheus_client';

    private string|bool $result = false;
    public function __construct(
        private readonly MetricRegistry $metricRegistry,
        private readonly EventDispatcherInterface $eventDispatcher
    ) {
    }

    public function renderMetrics(RetrieveMode $mode, ExtensionConfiguration $config): string|bool
    {
        // TODO: Allow configuration of storage adapter
        $metrics = $this->metricRegistry->getMetricsByRetrieveMode($mode);
        $this->eventDispatcher->dispatch(
            new BeforeMetricsRenderedEvent($metrics)
        );
        $collectorRegistry = new CollectorRegistry(new InMemory());
        foreach ($metrics as $metric) {
            try {
                if ($metric->getValue() == -1) {
                    continue;
                }
                match ($metric->getType()) {
                    MetricType::GAUGE => $this->renderGauge($collectorRegistry, $metric),
                    MetricType::COUNTER => $this->renderCounter($collectorRegistry, $metric),
                    MetricType::HISTOGRAM => $this->renderHistogram($collectorRegistry, $metric),
                    MetricType::SUMMARY => $this->renderSummary($collectorRegistry, $metric),
                };
            } catch (UnknownTypeException) {
            }
        }

        match ($mode) {
            RetrieveMode::SCRAPE => (function () use ($collectorRegistry): void {
                $renderer = new RenderTextFormat();
                $this->result = $renderer->render($collectorRegistry->getMetricFamilySamples());
            })(),
            RetrieveMode::PUSH => (function () use ($collectorRegistry, $config) {
                $gateway = new PushGateway($config->get(self::EXT_KEY, 'gateway'));
                $gateway->push($collectorRegistry, 't3_prom_client_push');
                $this->result = true;
            })(),
        };
        return $this->result;
    }

    protected function renderGauge(CollectorRegistry &$collectorRegistry, MetricInterface $metric): void
    {
        $collectorRegistry->registerGauge(
            $metric->getNamespace(),
            $metric->getName(),
            $metric->getHelp(),
            array_keys($metric->getLabels())
        )->set($metric->getValue(), array_values($metric->getLabels()));
    }

    protected function renderCounter(CollectorRegistry &$collectorRegistry, MetricInterface $metric): void
    {
        $collectorRegistry->registerCounter(
            $metric->getNamespace(),
            $metric->getName(),
            $metric->getHelp(),
            array_keys($metric->getLabels())
        )->incBy($metric->getValue(), array_values($metric->getLabels()));
    }

    protected function renderHistogram(CollectorRegistry &$collectorRegistry, MetricInterface $metric): void
    {
        $collectorRegistry->registerHistogram(
            $metric->getNamespace(),
            $metric->getName(),
            $metric->getHelp(),
            array_keys($metric->getLabels())
        )->observe($metric->getValue(), array_values($metric->getLabels()));
    }

    protected function renderSummary(CollectorRegistry &$collectorRegistry, MetricInterface $metric): void
    {
        $collectorRegistry->registerSummary(
            $metric->getNamespace(),
            $metric->getName(),
            $metric->getHelp(),
            array_keys($metric->getLabels())
        )->observe($metric->getValue(), array_values($metric->getLabels()));
    }
}
