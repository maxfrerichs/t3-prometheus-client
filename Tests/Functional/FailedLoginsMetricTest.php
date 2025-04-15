<?php

use MFR\T3PromClient\Metrics\FailedLoginsMetric;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

class FailedLoginsMetricTest extends FunctionalTestCase
{
    protected FailedLoginsMetric $subject;

    protected array $coreExtensionsToLoad = [
        'typo3/cms-core',
    ];

    public function setUp(): void
    {
        parent::setUp();
        $this->importCSVDataSet(__DIR__ . '/Fixtures/sys_log.csv');
        $this->subject = new FailedLoginsMetric();
    }

    public function testFailedLoginsMetricValue(): void
    {
        self::assertEquals(2, $this->subject->getValue());
    }
}
