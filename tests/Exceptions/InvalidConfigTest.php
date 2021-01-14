<?php

namespace Tests\Exceptions;

use Meveto\Client\Exceptions\InvalidConfig\ArchitectureNotSupportedException;
use Meveto\Client\Exceptions\InvalidConfig\ConfigNotSetException;
use Meveto\Client\Exceptions\InvalidConfig\StateNotSetException;
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
        $exception = new ConfigNotSetException();

        // assert message matches (partially).
        static::assertStringContainsString('client configuration is not set.', $exception->getMessage());
    }

    /**
     * Test InvalidConfig::architectureNotSupported()
     */
    public function testArchitectureNotSupported(): void
    {
        // create the exception instance.
        $exception = new ArchitectureNotSupportedException('arm', ['amd64', 'x86']);

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
        $exception = new StateNotSetException();

        // assert message matches (partially).
        static::assertStringContainsString(
            'Current application request state is not set.',
            $exception->getMessage()
        );
    }
}
