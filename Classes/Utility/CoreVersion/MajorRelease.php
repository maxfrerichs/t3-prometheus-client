<?php

declare(strict_types=1);

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

namespace MFR\T3PromClient\Utility\CoreVersion;

class MajorRelease
{
    protected $version;
    protected $lts;
    protected $title;
    protected $maintenanceWindow;

    public function __construct(string $version, ?string $lts, string $title, MaintenanceWindow $maintenanceWindow)
    {
        $this->version = $version;
        $this->lts = $lts;
        $this->title = $title;
        $this->maintenanceWindow = $maintenanceWindow;
    }

    public static function fromApiResponse(array $response): self
    {
        $maintenanceWindow = MaintenanceWindow::fromApiResponse($response);
        $ltsVersion = isset($response['lts']) ? (string)$response['lts'] : null;

        return new self((string)$response['version'], $ltsVersion, $response['title'], $maintenanceWindow);
    }

    public function getVersion(): string
    {
        return $this->version;
    }

    public function getLts(): ?string
    {
        return $this->lts;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getMaintenanceWindow(): MaintenanceWindow
    {
        return $this->maintenanceWindow;
    }
}
