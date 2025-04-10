<?php
declare(strict_types=1);
namespace MFR\T3PromClient\Metrics;

use MFR\T3PromClient\Enum\MetricType;
use MFR\T3PromClient\Enum\RetrieveMode;
use MFR\T3PromClient\Metrics\MetricInterface;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Connection;

final class SchedulerTaskMetric implements MetricInterface
{
    private MetricType $type = MetricType::GAUGE;

    private RetrieveMode $mode = RetrieveMode::SCRAPE;

    private string $name = "failed_scheduler_tasks";

    private array $labels = ["scheduler", "typo3"];

    private string $help = "Number of failed scheduler tasks";


    public function getName(): string
    {
        return $this->name;
    }

    public function getNamespace(): string
    {
        return self::DEFAULT_NAMESPACE;
    }

    public function getType(): MetricType
    {
        return MetricType::GAUGE;
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
        if (!ExtensionManagementUtility::isLoaded('scheduler')) {
            return -1;
        }

        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('tx_scheduler_task');
        $count = $queryBuilder
            ->count('uid')
            ->from('tx_scheduler_task')
            ->where(
                $queryBuilder->expr()->eq('deleted', $queryBuilder->createNamedParameter(0, Connection::PARAM_INT)),
                $queryBuilder->expr()->eq('disable', $queryBuilder->createNamedParameter(0, Connection::PARAM_INT)),
                $queryBuilder->expr()->neq('lastexecution_failure', $queryBuilder->createNamedParameter(''))
            )
            ->andWhere(
                $queryBuilder->expr()->eq('deleted', $queryBuilder->createNamedParameter(0, Connection::PARAM_INT))
            )->executeQuery()->fetchOne();
        return (int)$count;
    }
}
