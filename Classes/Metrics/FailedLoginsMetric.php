<?php
namespace MFR\T3PromClient\Metrics;

use MFR\T3PromClient\Enum\MetricType;
use MFR\T3PromClient\Enum\RetrieveMode;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;

final class FailedLoginsMetric implements MetricInterface
{
    protected string $name = "failed_logins";

    protected string $namespace = self::DEFAULT_NAMESPACE;

    protected MetricType $type = MetricType::HISTOGRAM;

    protected RetrieveMode $mode = RetrieveMode::SCRAPE;

    protected array $labels = [];

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
        $this->labels = [
            'context' => Environment::getContext()->__toString()
        ];
        return $this->labels;
    }

    public function getValue(): int
    {
        
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('sys_log');
        $queryBuilder->resetRestrictions();
        $queryBuilder->select('uid')->from('sys_log')
            ->where(
                $queryBuilder->expr()->eq('error', 3))
            ->andWhere(
                $queryBuilder->expr()->eq('type', 255)
            );

        $logCount = $queryBuilder->executeQuery()->rowCount();
        return (int)$logCount;
    }


}