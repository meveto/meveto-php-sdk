<?php

namespace Tests\Exceptions;

use Meveto\Client\Exceptions;
use PHPUnit\Framework\TestCase;

/**
 * Class InvalidConfigTest.
 */
class InvalidConfigTest extends TestCase
{
    /**
     * Test InvalidConfig::configNotSet()
     */
    public function testConfigNotSet(): void
    {
        // create the exception instance.
        $exception = Exceptions\InvalidConfig::configNotSet();

        // assert message matches (partially).
        static::assertStringContainsString('client configuration is not set.', $exception->getMessage());
    }

    /**
     * Test InvalidConfig::architectureNotSupported()
     */
    public function testArchitectureNotSupported(): void
    {
        // create the exception instance.
        $exception = Exceptions\InvalidConfig::architectureNotSupported('arm', ['amd64', 'x86']);

        // assert message matches (partially).
        static::assertStringContainsString(
            '`arm` is not supported',
            $exception->getMessage()
        );
        static::assertStringContainsString(
            'supported architectures include: `amd64`, `x86`.',
            $exception->getMessage()
        );
    }

    /**
     * Test InvalidConfig::stateNotSet()
     */
    public function testStateNotSet(): void
    {
        // create the exception instance.
        $exception = Exceptions\InvalidConfig::stateNotSet();

        // assert message matches (partially).
        static::assertStringContainsString(
            'Current application request state is not set.',
            $exception->getMessage()
        );
    }
}
