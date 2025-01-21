<?php

use MFR\T3PromClient\Authentication\AuthenticationFactory;
use MFR\T3PromClient\Authentication\BasicAuthentication;
use MFR\T3PromClient\Authentication\NoneAuthentication;
use MFR\T3PromClient\Authentication\TokenAuthentication;
use MFR\T3PromClient\Exception\InvalidArgumentException;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

class AuthenticationFactoryTest extends UnitTestCase
{
    protected AuthenticationFactory $subject;

    protected function setUp(): void
    {
        parent::setUp();
        $this->subject = new AuthenticationFactory();
    }
    public function testFactory(): void
    {
        self::assertInstanceOf(BasicAuthentication::class, $this->subject->createAuthenticator('basic'));
        self::assertInstanceOf(NoneAuthentication::class, $this->subject->createAuthenticator('none'));
        self::assertInstanceOf(TokenAuthentication::class, $this->subject->createAuthenticator('token'));

        $this->expectException(InvalidArgumentException::class);
        $this->subject->createAuthenticator('blubb');
    }
}
