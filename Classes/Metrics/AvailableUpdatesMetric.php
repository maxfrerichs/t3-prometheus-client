<?php
declare(strict_types=1);
namespace MFR\T3PromClient\Metrics;

use MFR\T3PromClient\Enum\MetricType;
use MFR\T3PromClient\Enum\RetrieveMode;
use MFR\T3PromClient\Metrics\MetricInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Install\Service\CoreVersionService;
use MFR\T3PromClient\Exception\RemoteFetchException;
final class AvailableUpdatesMetric implements MetricInterface
{
    private MetricType $type = MetricType::GAUGE;

    private RetrieveMode $mode = RetrieveMode::SCRAPE;

    private string $name = "available_updates";

    private array $labels = ["typo3", "security", "updates"];

    private string $help = "Number of available updates for this instance";

    public function getValue(): int|float
    {
        $coreVersionService = GeneralUtility::makeInstance(CoreVersionService::class);

        if (!$coreVersionService->isInstalledVersionAReleasedVersion()) {
            return 0;
        }

        try {
            $versionMaintenanceWindow = $coreVersionService->getMaintenanceWindow();
        } catch (RemoteFetchException $remoteFetchException) {
            return -1;
        }

        if (!$versionMaintenanceWindow->isSupportedByCommunity() && !$versionMaintenanceWindow->isSupportedByElts()) {
            return -2;
        }

        $availableReleases = [];
        $latestRelease = $coreVersionService->getYoungestPatchRelease();
        $isCurrentVersionElts = $coreVersionService->isCurrentInstalledVersionElts();

        if ($coreVersionService->isPatchReleaseSuitableForUpdate($latestRelease)) {
            $availableReleases[] = $latestRelease;
        }

        if (!$versionMaintenanceWindow->isSupportedByCommunity() && $latestRelease->isElts()) {
            $latestCommunityDrivenRelease = $coreVersionService->getYoungestCommunityPatchRelease();
            if ($coreVersionService->isPatchReleaseSuitableForUpdate($latestCommunityDrivenRelease)) {
                $availableReleases[] = $latestCommunityDrivenRelease;
            }
        }
        if ($availableReleases === []) {
            return 0;
        }

        $availableUpdates = 0;
        foreach ($availableReleases as $availableRelease) {
            if (($availableRelease->isElts() && $isCurrentVersionElts) || (!$availableRelease->isElts() && !$isCurrentVersionElts) ) {
                try {
                    if ($coreVersionService->isUpdateSecurityRelevant($availableRelease)) {
                        $availableUpdates++;
                    }
                } catch (RemoteFetchException $e) {
                    return 1;
                }
            }
        }
        return $availableUpdates;
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

    public function getHelp(): string
    {
        return $this->help;
    }

    public function getLabels(): array
    {
        return $this->labels;
    }

    public function getMode(): RetrieveMode
    {
        return $this->mode;
    }
}
