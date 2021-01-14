<?php

namespace Tests\Exceptions;

use Meveto\Client\Exceptions\Http\NotAuthenticatedException;
use Meveto\Client\Exceptions\Http\NotAuthorizedException;
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
        $exception = new NotAuthorizedException();

        // assert message matches (partially).
        static::assertStringContainsString('not authorized', $exception->getMessage());
    }

    /**
     * Test Http::notAuthenticated()
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
