<?php

declare(strict_types=1);
namespace MFR\T3PromClient\Metrics;

use MFR\T3PromClient\Enum\Type;
use MFR\T3PromClient\Exception\RemoteFetchException;
use MFR\T3PromClient\Service\CoreVersionService;
use TYPO3\CMS\Core\Utility\GeneralUtility;

final class AvailableUpdatesMetric extends AbstractMetric
{
    protected string $name = 'available_updates';

    protected Type $type = Type::GAUGE;

    protected string $help = 'Number of available updates for this instance';


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
        $availableUpdates = 0;

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
            return $availableUpdates;
        }

        foreach ($availableReleases as $availableRelease) {
            if (($availableRelease->isElts() && $isCurrentVersionElts) || (!$availableRelease->isElts() && !$isCurrentVersionElts)) {
                $availableUpdates++;
            }
        }
        return $availableUpdates;
    }
}
