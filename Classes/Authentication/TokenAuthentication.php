<?php

namespace MFR\T3PromClient\Authentication;

use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;

class TokenAuthentication implements AuthenticationInterface
{
    public function authenticate(ExtensionConfiguration $config, ServerRequestInterface $request): bool
    {
        $token = $config->get(self::EXT_KEY)['token'];

        if ($this->encodeCredentials($token) === $request->getHeaderLine('Authorization') 
            && $config->get(self::EXT_KEY)['port'] == $request->getServerParams()['SERVER_PORT']) 
        {
            return true;
        }
        return false;
    }

    public function getName(): string
    {
        return 'Token-based authentication';
    }

    private function encodeCredentials(string $token): string
    {
        return "Bearer ".$token;
    }
}
