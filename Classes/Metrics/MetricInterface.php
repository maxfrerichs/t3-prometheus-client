<?php

declare(strict_types=1);
namespace MFR\T3PromClient\Metrics;

use MFR\T3PromClient\Enum\MetricType;
use MFR\T3PromClient\Enum\RetrieveMode;

interface MetricInterface
{
    public const DEFAULT_NAMESPACE = 't3promclient';
    public function getName(): string;
    public function getNamespace(): string;
    public function getType(): MetricType;
    /**
     * @deprecated
     */
    public function getMode(): RetrieveMode;
    public function getHelp(): string;
    public function getLabels(): array;
    public function getValue(): int|float;
}
