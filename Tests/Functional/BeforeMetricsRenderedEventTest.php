<?php

declare(strict_types=1);

use MFR\T3PromClient\Enum\MetricType;
use MFR\T3PromClient\Enum\RetrieveMode;
use MFR\T3PromClient\Event\BeforeMetricsRenderedEvent;
use MFR\T3PromClient\Metrics\MetricInterface;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

class BeforeMetricsRenderedEventTest extends FunctionalTestCase
{
    protected array $testExtensionsToLoad = [
        't3_prometheus_client',
    ];

    public function testEventModifiesOriginalArrayByReference(): void
    {
        $metrics = [$this->createMetric('metric_1'), $this->createMetric('metric_2')];
        self::assertCount(2, $metrics, 'Should start with 2 metrics');
        $event = new BeforeMetricsRenderedEvent($metrics);
        $filteredMetrics = array_filter(
            $event->getMetrics(),
            fn(MetricInterface $m) => $m->getName() !== 'metric_1'
        );
        $event->setMetrics(array_values($filteredMetrics));

        // Verify the original array is modified
        self::assertCount(1, $metrics, 'Original array should be modified to have 1 metric');
        self::assertEquals('metric_2', $metrics[0]->getName());
    }

    public function testEventListenerCanAddMetricsToOriginalArray(): void
    {
        $metrics = [$this->createMetric('original')];
        self::assertCount(1, $metrics);

        $event = new BeforeMetricsRenderedEvent($metrics);

        // Simulate event listener adding a metric
        $currentMetrics = $event->getMetrics();
        $currentMetrics[] = $this->createMetric('added');
        $event->setMetrics($currentMetrics);

        // Verify original array now has 2 metrics
        self::assertCount(2, $metrics, 'Original array should now have 2 metrics');
        self::assertEquals('original', $metrics[0]->getName());
        self::assertEquals('added', $metrics[1]->getName());
    }


    private function createMetric(
        string $name,
        array $labels = [],
        MetricType $type = MetricType::GAUGE
    ): MetricInterface {
        return new class($name, $labels, $type) implements MetricInterface {
            public function __construct(
                private readonly string $name,
                private readonly array $labels,
                private readonly MetricType $type
            ) {}

            public function getName(): string { return $this->name; }
            public function getNamespace(): string { return 't3promclient'; }
            public function getType(): MetricType { return $this->type; }
            public function getMode(): RetrieveMode { return RetrieveMode::SCRAPE; }
            public function getHelp(): string { return 'Test metric'; }
            public function getLabels(): array { return $this->labels; }
            public function getValue(): int|float { return 1; }
        };
    }
}
