<?php
namespace MFR\Typo3Prometheus\Event;
use TYPO3\CMS\Reports\StatusProviderInterface;

final class RegisterStatusProviderEvent
{
    public function __construct(
        private array &$statusProviders
    )
    {}

    public function injectStatusProvider(StatusProviderInterface $statusProvider)
    {
        array_push($this->statusProviders, $statusProvider);
    }

    public function getStatusProvdiers()
    {
        return $this->statusProviders;
    }
}