<?php

namespace Tests\Exceptions;

use Meveto\Client\Exceptions\InvalidClient\ClientErrorException;
use Meveto\Client\Exceptions\InvalidClient\ClientNotFoundException;
use Tests\MevetoTestCase;

/**
 * Class InvalidClientTest.
 */
class InvalidClientTest extends MevetoTestCase
{
    /**
     * Test ClientNotFoundException.
     */
    public function testClientNotFound(): void
    {
        // create the exception instance.
        $exception = new ClientNotFoundException();

        // get exception message.
        $message = $exception->getMessage();

        // assert message matches (partially).
        static::assertStringContainsString('Your Meveto client credentials are incorrect.', $message);
        static::assertStringContainsString('Check your client ID, secret and redirect URL.', $message);
        static::assertStringContainsString('Check your client ID, secret and redirect URL.', $message);
        static::assertStringContainsString('Redirect URL must be exactly the same as', $message);
        static::assertStringContainsString('provided at the time of client registration.', $message);
    }

    /**
     * Test ClientErrorException.
     */
    public function testClientError(): void
    {
        // create the exception instance.
        $exception = new ClientErrorException('some-custom-error');

        // assert message matches (partially).
        static::assertStringContainsString('following error', $exception->getMessage());
        static::assertStringContainsString('some-custom-error', $exception->getMessage());
    }
}
