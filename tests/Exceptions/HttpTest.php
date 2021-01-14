<?php

namespace Tests\Exceptions;

use Meveto\Client\Exceptions\Http\NotAuthenticatedException;
use Meveto\Client\Exceptions\Http\NotAuthorizedException;
use Tests\MevetoTestCase;

/**
 * Class HttpTest.
 */
class HttpTest extends MevetoTestCase
{
    /**
     * Test NotAuthorizedException.
     */
    public function testNotAuthorizedException(): void
    {
        // create the exception instance.
        $exception = new NotAuthorizedException();

        // assert message matches (partially).
        static::assertStringContainsString('not authorized', $exception->getMessage());
    }

    /**
     * Test NotAuthenticatedException.
     */
    public function testNotAuthenticatedException(): void
    {
        // create the exception instance.
        $exception = new NotAuthenticatedException();

        // assert message matches (partially).
        static::assertStringContainsString('could not authenticate', $exception->getMessage());
    }

    /**
     * Test NotAuthenticatedException with custom code.
     */
    public function testCustomCodeNotAuthenticatedException(): void
    {
        // create the exception instance.
        $exception = new NotAuthenticatedException('', 999);

        // assert code matches.
        static::assertEquals(999, $exception->getCode());
    }
}
