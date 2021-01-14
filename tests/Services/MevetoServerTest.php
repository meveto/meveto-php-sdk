<?php

namespace Tests\Services;

use GuzzleHttp\Client;
use Meveto\Client\Exceptions\InvalidConfig\ArchitectureNotSupportedException;
use Meveto\Client\Exceptions\Validation\StateRequiredException;
use Meveto\Client\Exceptions\Validation\StateTooShortException;
use Meveto\Client\Services\MevetoServer;
use Tests\MevetoTestCase;

/**
 * Class MevetoServerTest.
 */
class MevetoServerTest extends MevetoTestCase
{
    /**
     * Test custom stateLength.
     */
    public function testStateLength(): void
    {
        // define custom value.
        $customLength = 123;

        // start  a server instance.
        $server = new MevetoServer();

        // set value.
        $server->stateLength($customLength);

        // extract internal value.
        $intervalValue = $this->getProtectedPropertyValue($server, 'stateLength');

        // assert the same.
        static::assertEquals($customLength, $intervalValue);

        // assert errors are matching custom size.
        try {
            $server->state('foo');
        } catch (\Exception $e) {
            static::assertStringContainsString("at least `{$customLength}` characters long.", $e->getMessage());
        }
    }

    /**
     * Test constructor (with custom client).
     */
    public function testConstructorWithClient(): void
    {
        $client = $this->createMock(Client::class);

        $mevetoServer = new MevetoServer($client);

        static::assertSame($client, $mevetoServer->http());
    }

    /**
     * Test constructor (without default client).
     */
    public function testConstructorWithoutClient(): void
    {
        $mevetoServer = new MevetoServer();

        static::assertInstanceOf(Client::class, $mevetoServer->http());
    }

    /**
     * Test http client late override.
     */
    public function testHttpClientOverride(): void
    {
        // create http client mock.
        $http = $this->createMock(Client::class);

        // create a server instance.
        $mevetoServer = new MevetoServer();
        // get original meveto server http client.
        $initialHttp = $mevetoServer->http();

        // make sure it's not the one we created.
        static::assertNotSame($http, $initialHttp);

        // overload/replace the http client.
        $mevetoServer->http($http);

        // get the overloaded/replaced http client.
        $replacedHttp = $mevetoServer->http();

        // asset the same.
        static::assertSame($replacedHttp, $http);
    }

    /**
     * Test state related methods.
     */
    public function testState(): void
    {
        // start  a server instance.
        $server = new MevetoServer();

        try {
            $server->state('');
        } catch (\Exception $e) {
            static::assertInstanceOf(StateRequiredException::class, $e);
        }

        try {
            $server->state($this->generateRandomState(10));
        } catch (\Exception $e) {
            static::assertInstanceOf(StateTooShortException::class, $e);
        }

        // generate a valid state string.
        $validState = $this->generateRandomState(128);

        // set state on server object.
        $server->state($validState);

        // get config.
        /** @var array $config */
        $config = $this->getProtectedPropertyValue($server, 'config');

        // assert same state.
        static::assertEquals($validState, $config['state']);

        // set state on server object (with empty spaced around it).
        $server->state(' ' . $validState . ' ');

        // get config.
        /** @var array $config */
        $config = $this->getProtectedPropertyValue($server, 'config');

        // assert still the same state.
        static::assertEquals($validState, $config['state']);
    }

    /**
     * Test aliasEndpoint() method.
     */
    public function testAliasEndpoint(): void
    {
        // define endpoint to use for the test.
        $testEndpoint = 'https://api.meveto.com/alias/endpoint';

        // start  a server instance.
        $server = new MevetoServer();

        // set endpoint.
        $server->aliasEndpoint($testEndpoint);

        // extract internal value.
        $intervalValue = $this->getProtectedPropertyValue($server, 'aliasEndpoint');

        // assert the same.
        static::assertEquals($testEndpoint, $intervalValue);
    }

    /**
     * Test setting eventUserEndpoint
     */
    public function testEventUserEndpoint(): void
    {
        // define endpoint to use for the test.
        $testEndpoint = 'https://api.meveto.com/event/user/testing';

        // start  a server instance.
        $server = new MevetoServer();

        // set endpoint.
        $server->eventUserEndpoint($testEndpoint);

        // extract internal value.
        $intervalValue = $this->getProtectedPropertyValue($server, 'eventUserEndpoint');

        // assert the same.
        static::assertEquals($testEndpoint, $intervalValue);
    }

    /**
     * Test setting resourceEndpoint
     */
    public function testResourceEndpoint(): void
    {
        // define endpoint to use for the test.
        $testEndpoint = 'https://api.meveto.com/resource/endpoint/testing';

        // start  a server instance.
        $server = new MevetoServer();

        // set endpoint.
        $server->resourceEndpoint($testEndpoint);

        // extract internal value.
        $intervalValue = $this->getProtectedPropertyValue($server, 'resourceEndpoint');

        // assert the same.
        static::assertEquals($testEndpoint, $intervalValue);
    }

    /**
     * Test setting architecture.
     */
    public function testArchitecturesMethod(): void
    {
        // define custom value.
        $architecture = 'alien-tech';

        // start  a server instance.
        $server = new MevetoServer();

        try {
            // set endpoint.
            $server->architecture($architecture);
        } catch (\Exception $e) {
            // assert unsupported is properly rejected.
            static::assertStringContainsString("`{$architecture}` is not supported", $e->getMessage());
            // assert error instance.
            static::assertInstanceOf(ArchitectureNotSupportedException::class, $e);
        }

        // now try a valid one.
        $architecture = 'web';
        // set a valid architecture.
        $server->architecture('web');
        // extract internal value.
        $intervalValue = $this->getProtectedPropertyValue($server, 'architecture');

        // assert the same.
        static::assertEquals($architecture, $intervalValue);
    }

    /**
     * Get a protected/private value from a given server instance.
     *
     * @param MevetoServer $server
     * @param string $propertyName
     *
     * @return mixed
     */
    protected function getProtectedPropertyValue(MevetoServer $server, string $propertyName)
    {
        try {
            // reflect to get config..
            $reflection = new \ReflectionClass($server);
            $property = $reflection->getProperty($propertyName);
            $property->setAccessible(true);

            return $property->getValue($server);
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Generates state of given length.
     *
     * @param int $length
     *
     * @throws
     *
     * @return string|null
     *
     */
    protected function generateRandomState($length = 128): string
    {
        return mb_substr(base64_encode(utf8_encode(random_bytes($length))), 0, $length);
    }
}
