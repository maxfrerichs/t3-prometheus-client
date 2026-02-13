<?php

declare(strict_types=1);
namespace MFR\T3PromClient\Metrics;

use MFR\T3PromClient\Enum\MetricType;
use MFR\T3PromClient\Enum\RetrieveMode;
use MFR\T3PromClient\Enum\RetrieveModeMode;
use TYPO3\CMS\Core\Core\Environment;

abstract class AbstractMetric implements MetricInterface
{
    protected string $name;
    protected string $namespace = self::DEFAULT_NAMESPACE;
    protected MetricType $type;
    protected RetrieveMode $mode = RetrieveMode::SCRAPE;
    protected string $help;
    protected array $labels = [];

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

    /**
     * @deprecated
     */
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
        if (empty($this->labels)) {
            $this->labels = $this->getDefaultLabels();
        }
        return $this->labels;
    }

    protected function getDefaultLabels(): array
    {
        return [
            'context' => Environment::getContext()->__toString(),
        ];
    }
}