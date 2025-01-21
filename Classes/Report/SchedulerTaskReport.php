<?php

namespace MFR\T3PromClient\Report;

/**
 * This file is part of the "zabbix_client" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Type\ContextualFeedbackSeverity;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Reports\Status;
use TYPO3\CMS\Reports\StatusProviderInterface;

class SchedulerTaskReport implements StatusProviderInterface
{
    public function getStatus(): array
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('tx_scheduler_task');
        $count = $queryBuilder
            ->count('uid')
            ->from('tx_scheduler_task')
            ->where(
                $queryBuilder->expr()->eq('deleted', $queryBuilder->createNamedParameter(0, Connection::PARAM_INT)),
                $queryBuilder->expr()->eq('disable', $queryBuilder->createNamedParameter(0, Connection::PARAM_INT)),
                $queryBuilder->expr()->neq('lastexecution_failure', $queryBuilder->createNamedParameter(''))
            )->andWhere(
                $queryBuilder->expr()->eq('deleted', $queryBuilder->createNamedParameter(0, Connection::PARAM_INT))
            )->executeQuery()->fetchOne();
        $status = [];
        match ($count) {
            $count == 0 => $status = new Status(
                title: 'Scheduler task status',
                severity: ContextualFeedbackSeverity::OK,
                message: 'No failed Scheduler tasks',
                value: (string)$count
            ),
            $count >= 1 => $status = new Status(
                title: 'Scheduler task status',
                severity: ContextualFeedbackSeverity::ERROR,
                message: 'Scheduler tasks failed to run. Please check your configuration',
                value: (string)$count
            )
        };
        return [$status];
    }

    public function getLabel(): string
    {
        return 'Scheduler task status';
    }
}
