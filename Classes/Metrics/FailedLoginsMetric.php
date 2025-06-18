<?php

namespace MFR\T3PromClient\Metrics;

use MFR\T3PromClient\Enum\Type;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;

final class FailedLoginsMetric extends AbstractMetric
{
    protected string $name = 'failed_logins';

    protected Type $type = Type::HISTOGRAM;

    protected string $help = 'Number of failed logins for this instance';


    public function getValue(): int
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('sys_log');
        $queryBuilder->resetRestrictions();
        $queryBuilder->select('uid')->from('sys_log')
            ->where(
                $queryBuilder->expr()->eq('error', 3)
            )
            ->andWhere(
                $queryBuilder->expr()->eq('type', 255)
            );
        $logCount = $queryBuilder->executeQuery()->rowCount();
        return (int)$logCount;
    }
}
