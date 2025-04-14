<?php

namespace MFR\T3PromClient\Authentication;

use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;

class BasicAuthentication implements AuthenticationInterface
{
    public function authenticate(ExtensionConfiguration $config, ServerRequestInterface $request): bool
    {
        $username = $config->get(self::EXT_KEY)['basicAuth']['username'];
        $password = $config->get(self::EXT_KEY)['basicAuth']['password'];

        if ($this->encodeCredentials($username, $password) === $request->getHeaderLine('Authorization') 
            && $config->get(self::EXT_KEY)['port'] == $request->getServerParams()['SERVER_PORT']) 
        {
            return true;
        }
        return false;
    }

    public function getName(): string
    {
        return 'HTTP basic authentication';
    }

    private function encodeCredentials(string $username, string $password): string
    {
        return "Basic ".base64_encode($username . ':' . $password);
    }
}
