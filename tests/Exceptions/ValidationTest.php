<?php

namespace Tests\Exceptions;

use Meveto\Client\Exceptions\Validation\InputDataInvalidException;
use Meveto\Client\Exceptions\Validation\KeyNotValidException;
use Meveto\Client\Exceptions\Validation\StateRequiredException;
use Meveto\Client\Exceptions\Validation\StateTooShortException;
use Meveto\Client\Exceptions\Validation\ValueNotValidAtException;
use Meveto\Client\Exceptions\Validation\ValueRequiredAtException;
use Tests\MevetoTestCase;

/**
 * Class ValidationTest.
 */
class ValidationTest extends MevetoTestCase
{
    /**
     * Test ValueRequiredAtException.
     */
    public function testValueRequiredAt()
    {
        // create the exception instance.
        $exception = new ValueRequiredAtException('place', 'foo');

        // assert message matches (partially).
        static::assertContains(
            '`foo` is required inside `place` array',
            $exception->getMessage()
        );
    }

    /**
     * Test ValueNotValidAtException.
     */
    public function testValueNotValidAt()
    {
        // create the exception instance.
        $exception = new ValueNotValidAtException('place', 'foo', 'bar');

        // assert message matches (partially).
        static::assertContains(
            '`foo` has an invalid value inside `place` array. It must be a valid `bar`',
            $exception->getMessage()
        );
    }

    /**
     * Test KeyNotValidException.
     */
    public function testKeyNotValid()
    {
        // create the exception instance.
        $exception = new KeyNotValidException('foo', 'bar');

        // assert message matches (partially).
        static::assertContains(
            'Your `foo` array has an unexpected key `bar`.',
            $exception->getMessage()
        );
    }

    /**
     * Test InputDataInvalidException.
     */
    public function testInputDataInvalid()
    {
        // create the exception instance.
        $exception = new InputDataInvalidException(['a', 'b']);

        // assert message matches (partially).
        static::assertContains(
            'The following errors occurred while processing your request: (`a`, `b`)',
            $exception->getMessage()
        );
    }

    /**
     * Test StateRequiredException.
     */
    public function testStateRequired()
    {
        // create the exception instance.
        $exception = new StateRequiredException();

        // assert message matches (partially).
        static::assertContains(
            'Current application request state can not be empty.',
            $exception->getMessage()
        );
    }

    /**
     * Test StateTooShortException.
     */
    public function testStateTooShort()
    {
        // create the exception instance.
        $exception = new StateTooShortException(42);

        // assert message matches (partially).
        static::assertContains(
            'Current application request state must be at least `42` characters long.',
            $exception->getMessage()
        );
    }
}
