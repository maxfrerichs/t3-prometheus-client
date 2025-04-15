<?php

declare(strict_types=1);

namespace MFR\T3PromClient\Registry;

use MFR\T3PromClient\Enum\RetrieveMode;
use MFR\T3PromClient\Metrics\MetricInterface;

/**
 * Registry for Prometheus-readable metrics. The registry receives all services, tagged with "prometheus.metric".
 * The tagging of metrics is automatically done based on the implemented MetricInterface.
 * @internal
 */
class MetricRegistry
{
    /**
     * @var MetricInterface[]
     */
    private array $metrics = [];

    /**
     * @param iterable<MetricInterface> $metrics
     */
    public function __construct(iterable $metrics)
    {
        foreach ($metrics as $metric) {
            $this->metrics[$metric->getName()] = $metric;
        }
    }

    /**
     * Get all registered metrics
     *
     * @return MetricInterface[]
     */
    public function getMetrics(): array
    {
        return $this->metrics;
    }

    /**
     * Filter registered by their retrieve mode
     *
     * @return MetricInterface[]
     */
    public function getMetricsByRetrieveMode(RetrieveMode $mode): array
    {
        foreach ($this->metrics as $metric) {
            if ($metric->getMode() != $mode) {
                unset($this->metrics[$metric->getName()]);
            }
        }
        return $this->metrics;
    }

    public function hasMetric(string $identifier): bool
    {
        return isset($this->metrics[$identifier]);
    }

    /**
     * Get registered metrics by identifier
     */
    public function getMetric(string $identifier): MetricInterface
    {
        if (!$this->hasMetric($identifier)) {
            throw new \UnexpectedValueException('Metric with identifier ' . $identifier . ' is not registered.', 1647241087);
        }

        return $this->metrics[$identifier];
    }
}
