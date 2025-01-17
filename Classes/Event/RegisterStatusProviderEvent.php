<?php

namespace MFR\T3PromClient\Event;

use TYPO3\CMS\Reports\StatusProviderInterface;

final class RegisterStatusProviderEvent
{
    /**
     * @param array<int,mixed> $statusProviders
     */
    public function __construct(
        private array &$statusProviders
    ) {
    }

    public function injectStatusProvider(StatusProviderInterface $statusProvider): void
    {
        array_push($this->statusProviders, $statusProvider);
    }

    public function getStatusProvdiers(): array
    {
        return $this->statusProviders;
    }
}
