<?php

declare(strict_types=1);

namespace MFR\T3PromClient\Authentication;

use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;

class TokenAuthentication implements AuthenticationInterface
{
    public function authenticate(ExtensionConfiguration $config, ServerRequestInterface $request): bool
    {
        try {
            $extensionConfiguration = $config->get(self::EXT_KEY);
        } catch (\Throwable) {
            return false;
        }

        $token = (string)($extensionConfiguration['token'] ?? '');

        if ($token === '') {
            return false;
        }

        if (!$this->isExpectedPort($extensionConfiguration, $request)) {
            return false;
        }

        return hash_equals(
            $this->encodeCredentials($token),
            $request->getHeaderLine('Authorization')
        );
    }

    public function getName(): string
    {
        return 'Token-based authentication';
    }

    /**
     * @param array<string, mixed> $extensionConfiguration
     */
    private function isExpectedPort(array $extensionConfiguration, ServerRequestInterface $request): bool
    {
        $configuredPort = (string)($extensionConfiguration['port'] ?? '');
        $requestPort = (string)($request->getServerParams()['SERVER_PORT'] ?? '');

        return $configuredPort !== '' && $configuredPort === $requestPort;
    }

    private function encodeCredentials(string $token): string
    {
        return 'Bearer ' . $token;
    }
}
