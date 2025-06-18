<?php

declare(strict_types=1);
namespace MFR\T3PromClient\Metrics;

use MFR\T3PromClient\Enum\Type;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

final class SchedulerTaskMetric extends AbstractMetric
{
    protected string $name = 'failed_scheduler_tasks';

    protected Type $type = Type::GAUGE;

    protected string $help = 'Number of failed scheduler tasks';


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
