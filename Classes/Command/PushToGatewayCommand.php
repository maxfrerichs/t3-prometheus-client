<?php

declare(strict_types=1);

namespace MFR\T3PromClient\Command;

use MFR\T3PromClient\Enum\RetrieveMode;
use MFR\T3PromClient\Service\PrometheusService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;

final class PushToGatewayCommand extends Command
{
    public function __construct(
        private readonly PrometheusService $promService,
        private readonly ExtensionConfiguration $config,
    ){
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setHelp('This command pushes Prometheus metrics to a specified gateway. Useful for complex metrics that cannot be scraped.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if (!$this->promService->renderMetrics(RetrieveMode::PUSH, $this->config)) {
            return Command::FAILURE;
        }
        return Command::SUCCESS;
    }
}
