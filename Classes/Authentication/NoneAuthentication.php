<?php

declare(strict_types=1);

namespace MFR\T3PromClient\Authentication;

use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;

class NoneAuthentication implements AuthenticationInterface
{
    public function authenticate(ExtensionConfiguration $config, ServerRequestInterface $request): bool
    {
        try {
            $extensionConfiguration = $config->get(self::EXT_KEY);
        } catch (\Throwable) {
            return false;
        }

        $configuredPort = (string)($extensionConfiguration['port'] ?? '');
        $requestPort = (string)($request->getServerParams()['SERVER_PORT'] ?? '');

        return $configuredPort !== '' && $configuredPort === $requestPort;
    }

    public function getName(): string
    {
        return 'Dummy authentication';
    }
}
