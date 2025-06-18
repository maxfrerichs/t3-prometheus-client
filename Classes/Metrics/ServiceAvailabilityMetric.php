<?php
declare(strict_types=1);
namespace MFR\T3PromClient\Metrics;

use MFR\T3PromClient\Enum\Type;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;

final class ServiceAvailabilityMetric extends AbstractMetric
{
    protected string $name = 'service_availability';
    protected Type $type = Type::HISTOGRAM;
    protected string $help = 'Number of logged ServiceUnavailableExceptions for this instance';

    public function getValue(): int
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('sys_log');
        $queryBuilder->resetRestrictions();
        $queryBuilder->select('uid')->from('sys_log')
            ->where(
                $queryBuilder->expr()->eq('error', 2)
            )
            ->andWhere(
                $queryBuilder->expr()->eq('type', 5)
            )
            ->andWhere(
                $queryBuilder->expr()->like(
                    'details',
                    $queryBuilder->quote('%ServiceUnavailableException%')
                )
            );

        $logCount = $queryBuilder->executeQuery()->rowCount();
        return (int)$logCount;
    }
}
