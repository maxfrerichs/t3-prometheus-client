<?php

declare(strict_types=1);
namespace MFR\T3PromClient\Metrics;

use MFR\T3PromClient\Enum\Type;
use MFR\T3PromClient\Enum\Mode;

interface MetricInterface
{
    public const DEFAULT_NAMESPACE = 't3promclient';
    public function getName(): string;
    public function getNamespace(): string;
    public function getType(): Type;
    public function getMode(): Mode;
    public function getHelp(): string;
    public function getLabels(): array;
    public function getValue(): int|float;
}
