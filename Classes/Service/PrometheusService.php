<?php

namespace MFR\T3PromClient\Service;

use MFR\T3PromClient\Enum\Type;
use MFR\T3PromClient\Event\BeforeMetricsRenderedEvent;
use MFR\T3PromClient\Exception\UnknownTypeException;
use MFR\T3PromClient\Metrics\MetricInterface;
use MFR\T3PromClient\Registry\MetricRegistry;
use MFR\T3PromClient\Storage\PromClientPDO;
use Prometheus\CollectorRegistry;
use Prometheus\RenderTextFormat;
use Prometheus\Storage\PDO;

use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Log\LoggerInterface;
use TYPO3\CMS\Core\SingletonInterface;

class PrometheusService implements SingletonInterface
{
    const EXT_KEY = 't3_prometheus_client';

    public function __construct(
        private readonly MetricRegistry $metricRegistry,
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly PromClientPDO $db,
        private readonly LoggerInterface $logger
    ) {}

    public function write(bool $refresh): void
    {
        $metrics = $this->metricRegistry->getMetrics();
        $this->eventDispatcher->dispatch(
            new BeforeMetricsRenderedEvent($metrics)
        );

        $collectorRegistry = new CollectorRegistry(new PDO(database: $this->db), false);

        if ($refresh) {
            $collectorRegistry->wipeStorage();
        }

        foreach ($metrics as $metric) {
            try {
                match ($metric->getType()) {
                    Type::GAUGE => $this->renderGauge($collectorRegistry, $metric),
                    Type::COUNTER => $this->renderCounter($collectorRegistry, $metric),
                    Type::HISTOGRAM => $this->renderHistogram($collectorRegistry, $metric),
                    Type::SUMMARY => $this->renderSummary($collectorRegistry, $metric),
                };
            } catch (UnknownTypeException $e) {
                $this->logger->warning($e);
                continue;
            }
        }
    }


    public function read(): string
    {
        $collectorRegistry = new CollectorRegistry(new PDO(database: $this->db), false);
        $renderer = new RenderTextFormat();
        return $renderer->render($collectorRegistry->getMetricFamilySamples());
    }

    public function refresh(): void
    {
        $this->write(refresh: true);
    }

    protected function renderGauge(CollectorRegistry &$collectorRegistry, MetricInterface $metric): void
    {
        $gauge = $collectorRegistry->getOrRegisterGauge(
            $metric->getNamespace(),
            $metric->getName(),
            $metric->getHelp(),
            array_keys($metric->getLabels())
        );
        $gauge->set(
            $metric->getValue(),
            array_values($metric->getLabels())
        );
    }

    protected function renderCounter(CollectorRegistry &$collectorRegistry, MetricInterface $metric): void
    {
        $counter = $collectorRegistry->getOrRegisterCounter(
            $metric->getNamespace(),
            $metric->getName(),
            $metric->getHelp(),
            array_keys($metric->getLabels())
        );
        $counter->incBy(
            $metric->getValue(),
            array_values($metric->getLabels())
        );
    }

    protected function renderHistogram(CollectorRegistry &$collectorRegistry, MetricInterface $metric): void
    {
        $histogram = $collectorRegistry->getOrRegisterHistogram(
            $metric->getNamespace(),
            $metric->getName(),
            $metric->getHelp(),
            array_keys($metric->getLabels())
        );
        $histogram->observe(
            $metric->getValue(),
            array_values($metric->getLabels())
        );
    }

    protected function renderSummary(CollectorRegistry &$collectorRegistry, MetricInterface $metric): void
    {
        $summary = $collectorRegistry->getOrRegisterSummary(
            $metric->getNamespace(),
            $metric->getName(),
            $metric->getHelp(),
            array_keys($metric->getLabels())
        );
        $summary->observe(
            $metric->getValue(),
            array_values($metric->getLabels())
        );
    }
}
