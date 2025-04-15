<?php

declare(strict_types=1);

namespace MFR\T3PromClient\Event;

use MFR\T3PromClient\Metrics\MetricInterface;

/**
 * This event allows to modify the list of retrieved metrics
 */
final class BeforeMetricsRenderedEvent
{
    /**
     * @var MetricInterface[]
     */
    private array $metrics;

    public function __construct(array &$metrics)
    {
        $this->metrics = $metrics;
    }

    /**
     * @return MetricInterface[]
     */
    public function getMetrics()
    {
        return $this->metrics;
    }

    public function setMetrics(array $metrics)
    {
        $this->metrics = $metrics;
    }
}
