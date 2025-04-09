<?php
declare(strict_types=1);
namespace MFR\T3PromClient\Metrics;

use MFR\T3PromClient\Enum\MetricType;
use MFR\T3PromClient\Enum\RetrieveMode;
use MFR\T3PromClient\Metrics\MetricInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Connection;

class SchedulerTaskMetric implements MetricInterface
{
    private MetricType $type = MetricType::GAUGE;

    private RetrieveMode $mode = RetrieveMode::SCRAPE;
    
    private string $name = "failed_scheduler_tasks";
    
    private array $labels = [""];
    
    private string $help = "";
    /**
     * @inheritDoc
     */
    public function getValue() 
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('tx_scheduler_task');
        $queryBuilder
            ->count('uid')
            ->from('tx_scheduler_task')
            ->where(
                $queryBuilder->expr()->eq('deleted', $queryBuilder->createNamedParameter(0, Connection::PARAM_INT)),
                $queryBuilder->expr()->eq('disable', $queryBuilder->createNamedParameter(0, Connection::PARAM_INT)),
                $queryBuilder->expr()->neq('lastexecution_failure', $queryBuilder->createNamedParameter(''))
            )
            ->andWhere(
                $queryBuilder->expr()->eq('deleted', $queryBuilder->createNamedParameter(0, Connection::PARAM_INT))
            );


        $count = $queryBuilder->executeQuery()->fetchOne();
        return $count;
    }

    
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
    /**
     * @inheritDoc
     */
    public function getHelp() 
    {
        return "";
    }
    
    /**
     * @inheritDoc
     */
    public function getLabels(): array 
    {
        return [];
    }
    /**
     * @inheritDoc
     */
    public function getMode(): RetrieveMode 
    {
        return $this->mode;
    }
}