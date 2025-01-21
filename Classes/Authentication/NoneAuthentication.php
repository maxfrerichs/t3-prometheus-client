<?php

namespace MFR\T3PromClient\Authentication;

use Psr\Http\Message\ServerRequestInterface;

use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;

class NoneAuthentication implements AuthenticationInterface
{
    public function authenticate(ExtensionConfiguration $config, ServerRequestInterface $request): bool
    {
        if ($config->get(self::EXTENSION_KEY)['port'] == $request->getServerParams()['SERVER_PORT']) {
            return true;
        }
        return false;
    }

    public function getName(): string
    {
        return 'Dummy authentication';
    }
}
