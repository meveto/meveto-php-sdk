<?php

namespace Tests\Exceptions;

use Meveto\Client\Exceptions;
use PHPUnit\Framework\TestCase;

/**
 * Class InvalidClientTest.
 */
class InvalidClientTest extends TestCase
{
    /**
     * Test InvalidClient::clientNotFound()
     */
    public function testClientNotFound(): void
    {
        // create the exception instance.
        $exception = Exceptions\InvalidClient::clientNotFound();

        // assert message matches (partially).
        static::assertStringContainsString('client credentials are incorrect', $exception->getMessage());
    }

    /**
     * Test InvalidClient::clientError()
     */
    public function testClientError(): void
    {
        // create the exception instance.
        $exception = Exceptions\InvalidClient::clientError('some-custom-error');

        // assert message matches (partially).
        static::assertStringContainsString('following error', $exception->getMessage());
        static::assertStringContainsString('some-custom-error', $exception->getMessage());
    }
}