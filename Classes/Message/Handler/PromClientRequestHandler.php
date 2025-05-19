<?php
declare(strict_types=1);
namespace MFR\T3PromClient\Message\Handler;

use MFR\T3PromClient\Message\PromClientRequestMessage;
use MFR\T3PromClient\Service\PrometheusService;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsMessageHandler]
final class PromClientRequestHandler
{
    public function __construct(
        private readonly PrometheusService $promService,
    ){

    }
    public function __invoke(PromClientRequestMessage $message): void
    {
        $this->promService->write(true);
    }
}
