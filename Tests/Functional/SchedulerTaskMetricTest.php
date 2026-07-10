<?php

use MFR\T3PromClient\Metrics\SchedulerTaskMetric;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

class SchedulerTaskMetricTest extends FunctionalTestCase
{
    protected SchedulerTaskMetric $subject;

    protected array $coreExtensionsToLoad = [
        'core',
        'scheduler',
    ];

    public function setUp(): void
    {
        parent::setUp();
        $this->subject = new SchedulerTaskMetric();
    }

    public function testSchedulerTaskMetric(): void
    {
        self::assertEquals(0, $this->subject->getValue());
    }
}
