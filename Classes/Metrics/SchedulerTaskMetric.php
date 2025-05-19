<?php

declare(strict_types=1);
namespace MFR\T3PromClient\Metrics;

use MFR\T3PromClient\Enum\MetricType;
use MFR\T3PromClient\Enum\Mode;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

final class SchedulerTaskMetric implements MetricInterface
{
    protected string $name = 'failed_scheduler_tasks';

    protected string $namespace = self::DEFAULT_NAMESPACE;

    protected MetricType $type = MetricType::GAUGE;

    protected Mode $mode = Mode::SCRAPE;

    protected array $labels = [];

    protected string $help = 'Number of failed scheduler tasks';

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

    public function getMode(): Mode
    {
        return $this->mode;
    }

    public function getHelp(): string
    {
        return $this->help;
    }

    public function getLabels(): array
    {
        $this->labels = [
            'context' => Environment::getContext()->__toString(),
        ];
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
