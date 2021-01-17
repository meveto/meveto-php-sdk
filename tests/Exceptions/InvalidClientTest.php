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
    public function testClientNotFound()
    {
        // create the exception instance.
        $exception = new ClientNotFoundException();

        // get exception message.
        $message = $exception->getMessage();

        // assert message matches (partially).
        static::assertContains('Your Meveto client credentials are incorrect.', $message);
        static::assertContains('Check your client ID, secret and redirect URL.', $message);
        static::assertContains('Check your client ID, secret and redirect URL.', $message);
        static::assertContains('Redirect URL must be exactly the same as', $message);
        static::assertContains('provided at the time of client registration.', $message);
    }

    /**
     * Test ClientErrorException.
     */
    public function testClientError()
    {
        // create the exception instance.
        $exception = new ClientErrorException('some-custom-error');

        // assert message matches (partially).
        static::assertContains('following error', $exception->getMessage());
        static::assertContains('some-custom-error', $exception->getMessage());
    }
}
