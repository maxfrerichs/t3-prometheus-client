<?php
namespace MFR\T3PromClient\Metrics;

use MFR\T3PromClient\Enum\MetricType;
use MFR\T3PromClient\Enum\RetrieveMode;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;

final class ServiceAvailabilityMetric implements MetricInterface
{
    protected string $name = "service_availability";

    protected string $namespace = self::DEFAULT_NAMESPACE;

    protected MetricType $type = MetricType::HISTOGRAM;

    protected RetrieveMode $mode = RetrieveMode::SCRAPE;

    protected array $labels = ["typo3", "availability", "exception"];

    protected string $help = "Number of logged ServiceUnavailableExceptions for this instance";

    public function getName(): string
    {
        return $this->name;
    }

    public function getNamespace(): string
    {
        return $this->namespace;
    }

    public function getType(): MetricType
    {
        return $this->type;
    }

    public function getMode(): RetrieveMode
    {
        return $this->mode;
    }

    public function getHelp(): string
    {
        return $this->help;
    }

    public function getLabels(): array
    {
        return $this->labels;
    }

    public function getValue(): int
    {
        
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('sys_log');
        $queryBuilder->resetRestrictions();
        $queryBuilder->select('uid')->from('sys_log')
            ->where(
                $queryBuilder->expr()->eq('error', 2))
            ->andWhere(
                $queryBuilder->expr()->eq('type',5))
            ->andWhere(
                $queryBuilder->expr()->like('details', $queryBuilder->quote('%ServiceUnavailableException%')
            )
        );

        $logCount = $queryBuilder->executeQuery()->rowCount();
        return (int)$logCount;
    }


}