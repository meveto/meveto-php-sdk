<?php

namespace Tests\Exceptions;

use Meveto\Client\Exceptions\InvalidConfig\ArchitectureNotSupportedException;
use Meveto\Client\Exceptions\InvalidConfig\ConfigNotSetException;
use Meveto\Client\Exceptions\InvalidConfig\StateNotSetException;
use Tests\MevetoTestCase;

/**
 * Class InvalidConfigTest.
 */
class InvalidConfigTest extends MevetoTestCase
{
    /**
     * Test ConfigNotSetException.
     */
    public function testConfigNotSet()
    {
        // create the exception instance.
        $exception = new ConfigNotSetException();

        // assert message matches (partially).
        static::assertStringHasString('client configuration is not set.', $exception->getMessage());
    }

    /**
     * Test ArchitectureNotSupportedException.
     */
    public function testArchitectureNotSupported()
    {
        // create the exception instance.
        $exception = new ArchitectureNotSupportedException('arm', ['amd64', 'x86']);

        // assert message matches (partially).
        static::assertStringHasString(
            '`arm` is not supported',
            $exception->getMessage()
        );
        static::assertStringHasString(
            'At the moment, supported architectures include: `amd64`, `x86`.',
            $exception->getMessage()
        );
    }

    /**
     * Test StateNotSetException.
     */
    public function testStateNotSet()
    {
        // create the exception instance.
        $exception = new StateNotSetException();

        // assert message matches (partially).
        static::assertStringHasString(
            'Current application request state is not set.',
            $exception->getMessage()
        );
    }
}
