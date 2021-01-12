<?php

namespace Tests\Exceptions;

use Meveto\Client\Exceptions;
use PHPUnit\Framework\TestCase;

/**
 * Class ValidationTest.
 */
class ValidationTest extends TestCase
{
    /**
     * Test Validation::valueRequiredAt()
     */
    public function testValueRequiredAt(): void
    {
        // create the exception instance.
        $exception = Exceptions\Validation::valueRequiredAt('place', 'foo');

        // assert message matches (partially).
        static::assertStringContainsString(
            '`foo` is required inside `place` array',
            $exception->getMessage()
        );
    }

    /**
     * Test Validation::valueNotValidAt()
     */
    public function testValueNotValidAt(): void
    {
        // create the exception instance.
        $exception = Exceptions\Validation::valueNotValidAt('place', 'foo', 'bar');

        // assert message matches (partially).
        static::assertStringContainsString(
            '`foo` has an invalid value inside `place` array. It must be a valid `bar`',
            $exception->getMessage()
        );
    }

    /**
     * Test Validation::keyNotValid()
     */
    public function testKeyNotValid(): void
    {
        // create the exception instance.
        $exception = Exceptions\Validation::keyNotValid('foo', 'bar');

        // assert message matches (partially).
        static::assertStringContainsString(
            'Your `foo` array has an unexpected key `bar`.',
            $exception->getMessage()
        );
    }

    /**
     * Test Validation::inputDataInvalid()
     */
    public function testInputDataInvalid(): void
    {
        // create the exception instance.
        $exception = Exceptions\Validation::inputDataInvalid([ 'a', 'b']);

        // assert message matches (partially).
        static::assertStringContainsString(
            'The following errors occurred while processing your request: (`a`, `b`)',
            $exception->getMessage()
        );
    }

    /**
     * Test Validation::stateRequired()
     */
    public function testStateRequired(): void
    {
        // create the exception instance.
        $exception = Exceptions\Validation::stateRequired();

        // assert message matches (partially).
        static::assertStringContainsString(
            'Current application request state can not be empty.',
            $exception->getMessage()
        );
    }

    /**
     * Test Validation::stateTooShort()
     */
    public function testStateTooShort(): void
    {
        // create the exception instance.
        $exception = Exceptions\Validation::stateTooShort(42);

        // assert message matches (partially).
        static::assertStringContainsString(
            'Current application request state must be at least `42` characters long.',
            $exception->getMessage()
        );
    }
}
