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
    public function testFactoryWithValidParams(): void
    {
        self::assertInstanceOf(BasicAuthentication::class, $this->subject->getAuthentication('basic'));
        self::assertInstanceOf(NoneAuthentication::class, $this->subject->getAuthentication('none'));
        self::assertInstanceOf(TokenAuthentication::class, $this->subject->getAuthentication('token'));
    }

    public function testFactoryWithInvalidParams(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->subject->getAuthentication('blubb');
    }
}
