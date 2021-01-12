<?php

namespace Tests\Exceptions;

use Meveto\Client\Exceptions;
use PHPUnit\Framework\TestCase;

/**
 * Class HttpTest.
 */
class HttpTest extends TestCase
{
    /**
     * Test Http::notAuthorized()
     */
    public function testNotAuthorizedException(): void
    {
        // create the exception instance.
        $exception = Exceptions\Http::notAuthorized();

        // assert message matches (partially).
        static::assertStringContainsString('not authorized', $exception->getMessage());
    }

    /**
     * Test Http::notAuthenticated()
     */
    public function testNotAuthenticatedException(): void
    {
        // create the exception instance.
        $exception = Exceptions\Http::notAuthenticated();

        // assert message matches (partially).
        static::assertStringContainsString('could not authenticate', $exception->getMessage());
    }
}
