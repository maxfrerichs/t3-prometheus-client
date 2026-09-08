<?php

declare(strict_types=1);

namespace MFR\T3PromClient\Authentication;

use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;

class BasicAuthentication implements AuthenticationInterface
{
    public function authenticate(ExtensionConfiguration $config, ServerRequestInterface $request): bool
    {
        try {
            $extensionConfiguration = $config->get(self::EXT_KEY);
        } catch (\Throwable) {
            return false;
        }

        $username = (string)($extensionConfiguration['basicAuth']['username'] ?? '');
        $password = (string)($extensionConfiguration['basicAuth']['password'] ?? '');

        if ($username === '' || $password === '') {
            return false;
        }

        if (!$this->isExpectedPort($extensionConfiguration, $request)) {
            return false;
        }

        return hash_equals(
            $this->encodeCredentials($username, $password),
            $request->getHeaderLine('Authorization')
        );
    }

    public function getName(): string
    {
        return 'HTTP basic authentication';
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

    private function encodeCredentials(string $username, string $password): string
    {
        return 'Basic ' . base64_encode($username . ':' . $password);
    }
}
