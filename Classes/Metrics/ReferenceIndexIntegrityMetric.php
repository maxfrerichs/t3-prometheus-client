<?php
declare(strict_types=1);
namespace MFR\T3PromClient\Metrics;
use MFR\T3PromClient\Enum\MetricType;
use MFR\T3PromClient\Enum\RetrieveMode;
use MFR\T3PromClient\Metrics\MetricInterface;
use TYPO3\CMS\Core\Database\ReferenceIndex;


class ReferenceIndexIntegrityMetric implements MetricInterface
{
    private string $name = "reference_index_integrity";

    private MetricType $type = MetricType::GAUGE;

    private RetrieveMode $mode = RetrieveMode::PUSH;

    private array $labels = ["integrity", "sys_refindex", "typo3"];

    private string $help = "Integrity of the TYPO3 reference index";

    public function __construct(
        private readonly ReferenceIndex $refIndex
    ){}

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
        //TODO: Find better way to determine refindex integrity.
        $errors = $this->refIndex->updateIndex(true);
        return count($errors);
    }
}
