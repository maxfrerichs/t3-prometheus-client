<?php

namespace MFR\T3PromClient\Authentication;

use MFR\T3PromClient\Exception\InvalidArgumentException;

class AuthenticationFactory
{
    public function getAuthentication(string $type): AuthenticationInterface
    {
        return match ($type) {
            'basic' => new BasicAuthentication(),
            'token' => new TokenAuthentication(),
            'none' => new NoneAuthentication(),
            default => throw new InvalidArgumentException("Unknown authenticator type: $type"),
        };
    }
}
