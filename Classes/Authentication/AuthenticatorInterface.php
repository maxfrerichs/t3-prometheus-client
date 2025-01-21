<?php

namespace MFR\T3PromClient\Authentication;

use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;

interface AuthenticatorInterface
{
    const EXTENSION_KEY = 't3_prometheus_client';
    public function authenticate(ExtensionConfiguration $config, ServerRequestInterface $request): bool;
    public function getName(): string;
}
