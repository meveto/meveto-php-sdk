<?php

namespace Tests\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Psr7\Response;
use Meveto\Client\Exceptions\Http\NotAuthenticatedException;
use Meveto\Client\Exceptions\Http\NotAuthorizedException;
use Meveto\Client\Exceptions\InvalidClient\ClientErrorException;
use Meveto\Client\Exceptions\InvalidClient\ClientNotFoundException;
use Meveto\Client\Exceptions\InvalidConfig\ArchitectureNotSupportedException;
use Meveto\Client\Exceptions\InvalidConfig\StateNotSetException;
use Meveto\Client\Exceptions\Validation\KeyNotValidException;
use Meveto\Client\Exceptions\Validation\StateRequiredException;
use Meveto\Client\Exceptions\Validation\StateTooShortException;
use Meveto\Client\Exceptions\Validation\ValueRequiredAtException;
use Meveto\Client\Services\MevetoServer;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Tests\MevetoTestCase;

/**
 * Class MevetoServerTest.
 */
class MevetoServerTest extends MevetoTestCase
{
    /**
     * Test custom stateLength.
     */
    public function testStateLength()
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
            static::assertContains("at least `{$customLength}` characters long.", $e->getMessage());
        }
    }

    /**
     * Test constructor (with custom client).
     */
    public function testConstructorWithClient()
    {
        $client = $this->createMock(Client::class);

        $mevetoServer = new MevetoServer($client);

        static::assertSame($client, $mevetoServer->http());
    }

    /**
     * Test constructor (without default client).
     */
    public function testConstructorWithoutClient()
    {
        $mevetoServer = new MevetoServer();

        static::assertInstanceOf(Client::class, $mevetoServer->http());
    }

    /**
     * Test http client late override.
     */
    public function testHttpClientOverride()
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
    public function testState()
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
    public function testAliasEndpoint()
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
    public function testEventUserEndpoint()
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
    public function testResourceEndpoint()
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
    public function testArchitecturesMethod()
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
            static::assertContains("`{$architecture}` is not supported", $e->getMessage());
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
     * Test getSupportedArchitectures method.
     */
    public function testGettingSupportedArchitectures()
    {
        // list of predefined architectures.
        $supported = ['web', 'rest'];

        // create a server instance.
        $server = new MevetoServer();

        // assert supported architectures matches predefined list.
        static::assertEquals($supported, $server->getSupportedArchitectures());
    }

    /**
     * Tests processLogin method.
     *
     * @throws StateNotSetException
     * @throws StateRequiredException
     * @throws StateTooShortException
     */
    public function testProcessLogin()
    {
        // create client mock.
        $client = $this->createMock(Client::class);

        // create server instance.
        $server = new MevetoServer($client);

        // fake client and sharing tokens.
        $clientToken = 'foo';
        $sharingToken = 'bar';

        // try without setting state first.
        try {
            $authQuery = $server->processLogin($clientToken, $sharingToken);
        } catch (\Exception $e) {
            // assert a state is required before process login.
            static::assertInstanceOf(StateNotSetException::class, $e);
        }

        // generate state.
        $state = $this->generateRandomState(128);
        // set state.
        $server->state($state);

        // do process login to get auth query.
        $authQuery = $server->processLogin($clientToken, $sharingToken);

        // assert all parts are in place.
        static::assertContains('client_token=foo', $authQuery);
        static::assertContains('sharing_token=bar', $authQuery);
        static::assertContains("state={$state}", $authQuery);
    }

    /**
     * Test accessToken method (valid settings).
     *
     * @throws
     */
    public function testAccessTokenMethod()
    {
        $tokenEndpoint = 'https://prod.meveto.com/oauth/token/foo/bar';
        $authCode = 'foo-bar-baz';

        $config = [
            'tokenEndpoint' => $tokenEndpoint,
            'id' => 'foo',
            'secret' => 'bar',
            'redirect_url' => 'https://foo.bar.com/back'
        ];

        // start mock http client.
        $http = $this->createMock(Client::class);

        // configure mock http client.
        $http->expects(static::once())
            ->method('request')
            ->with('POST', $tokenEndpoint, [
                'form_params' => [
                    'grant_type' => 'authorization_code',
                    'client_id' => $config['id'],
                    'client_secret' => $config['secret'],
                    'redirect_uri' => $config['redirect_url'],
                    'code' => $authCode,
                ]
            ])
            ->willReturn(new Response(200, [], '{ "access_token": "foo-access-token" }'));

        // start server instance with custom http.
        $server = new MevetoServer($http);

        // set config on server.
        $server->config($config);

        // call access code method.
        $accessToken = $server->accessToken($authCode);

        // assert it returns the response body.
        static::assertEquals([ 'access_token' => 'foo-access-token' ], $accessToken);
    }

    /**
     * Test resourceOwnerData method.
     *
     * @throws
     */
    public function testResourceOwnerDataMethod()
    {
        // fake token to test.
        $token = 'token-foo-token-bar-token-baz';

        // start mock http client.
        $http = $this->createMock(Client::class);

        // start server instance with custom http.
        $server = new MevetoServer($http);

        // get resource endpoint from internal value.
        /** @var string $resourceEndpoint */
        $resourceEndpoint = $this->getProtectedPropertyValue($server, 'resourceEndpoint');

        // get config as well from internal.
        /** @var array $config */
        $config = $this->getProtectedPropertyValue($server, 'config');

        // configure mock http client.
        $http->expects(static::once())
            ->method('request')
            ->with('GET', $resourceEndpoint, [
                'query' => [ 'client_id' => $config['id'] ],
                'headers' => [
                    'Accept' => 'application/json',
                    'Authorization' => 'Bearer '.$token
                ],
            ])
            ->willReturn(new Response(200, [], '{ "payload": { "foo": "bar" } }'));

        // call token method.
        $response = $server->resourceOwnerData($token);

        // assert it returns the response body.
        static::assertEquals([ 'foo' => 'bar' ], $response);
    }

    /**
     * Test resourceOwnerData method with generic error.
     *
     * @throws
     */
    public function testResourceOwnerDataMethodWithGenericError()
    {
        // fake token to test.
        $token = 'token-foo-token-bar-token-baz';

        // create custom mock exception.
        $mockException = new ClientException(
            'foo-bar-message',
            $this->createMock(RequestInterface::class),
            $this->createMock(ResponseInterface::class)
        );

        // start mock http client.
        $http = $this->createMock(Client::class);

        // start server instance with custom http.
        $server = new MevetoServer($http);

        // configure mock http client.
        $http->expects(static::once())
            ->method('request')
            ->willThrowException($mockException);

        try {
            // call token method.
            $response = $server->resourceOwnerData($token);
        } catch (ClientException $e) {
            static::assertContains('foo-bar-message', $e->getMessage());
        }

        // start mock http client.
        $http = $this->createMock(Client::class);

        // start server instance with custom http.
        $server = new MevetoServer($http);

        // configure mock http client.
        $http->expects(static::once())
            ->method('request')
            ->willReturn(new Response(401, [], '{}'));

        try {
            // call token method.
            $response = $server->resourceOwnerData($token);
        } catch (NotAuthenticatedException $e) {
            static::assertContains(
                'Meveto server could not authenticate your request and responded with a 401 status',
                $e->getMessage()
            );
        }

        // start mock http client.
        $http = $this->createMock(Client::class);

        // start server instance with custom http.
        $server = new MevetoServer($http);

        // configure mock http client.
        $http->expects(static::once())
            ->method('request')
            ->willReturn(new Response(403, [], '{}'));

        try {
            // call token method.
            $response = $server->resourceOwnerData($token);
        } catch (NotAuthorizedException $e) {
            self::assertContains(
                'is not authorized to',
                $e->getMessage()
            );
        }

        // start mock http client.
        $http = $this->createMock(Client::class);

        // start server instance with custom http.
        $server = new MevetoServer($http);

        // configure mock http client.
        $http->expects(static::once())
            ->method('request')
            ->willReturn(new Response(200, [], '{}'));

        try {
            // call token method.
            $response = $server->resourceOwnerData($token);
        } catch (ClientErrorException $e) {
            static::assertContains(
                'Meveto authorization server responded with the following error. Empty payload',
                $e->getMessage()
            );
        }

        // start mock http client.
        $http = $this->createMock(Client::class);

        // start server instance with custom http.
        $server = new MevetoServer($http);

        // configure mock http client.
        $http->expects(static::once())
            ->method('request')
            ->willReturn(new Response(200, [], '{ "error": "any", "error_description": "Custom Error Message Foo" }'));

        try {
            // call token method.
            $response = $server->resourceOwnerData($token);
        } catch (ClientErrorException $e) {
            static::assertContains(
                'Custom Error Message Foo',
                $e->getMessage()
            );
        }
    }

    /**
     * Test accessToken method (valid settings but with error).
     *
     * @throws
     */
    public function testAccessTokenMethodWithError()
    {
        $tokenEndpoint = 'https://prod.meveto.com/oauth/token/foo/bar';
        $authCode = 'foo-bar-baz';

        $config = [
            'tokenEndpoint' => $tokenEndpoint,
            'id' => 'foo',
            'secret' => 'bar',
            'redirect_url' => 'https://foo.bar.com/back'
        ];

        // start mock http client.
        $http = $this->createMock(Client::class);

        // configure mock http client.
        $http->expects(static::once())
            ->method('request')
            ->with('POST', $tokenEndpoint, [
                'form_params' => [
                    'grant_type' => 'authorization_code',
                    'client_id' => $config['id'],
                    'client_secret' => $config['secret'],
                    'redirect_uri' => $config['redirect_url'],
                    'code' => $authCode,
                ]
            ])
            ->willReturn(new Response(200, [], '{ "error": "invalid_client" }'));

        // start server instance with custom http.
        $server = new MevetoServer($http);

        // set config on server.
        $server->config($config);

        try {
            // call access code method.
            $accessToken = $server->accessToken($authCode);
        } catch (ClientNotFoundException $e) {
            // assert proper message.
            static::assertContains('Your Meveto client credentials are incorrect.', $e->getMessage());
        }
    }

    /**
     * Test accessToken method (valid settings but custom error message).
     *
     * @throws
     */
    public function testAccessTokenMethodWithCustomError()
    {
        $tokenEndpoint = 'https://prod.meveto.com/oauth/token/foo/bar';
        $authCode = 'foo-bar-baz';

        $config = [
            'tokenEndpoint' => $tokenEndpoint,
            'id' => 'foo',
            'secret' => 'bar',
            'redirect_url' => 'https://foo.bar.com/back'
        ];

        // start mock http client.
        $http = $this->createMock(Client::class);

        // configure mock http client.
        $http->expects(static::once())
            ->method('request')
            ->with('POST', $tokenEndpoint, [
                'form_params' => [
                    'grant_type' => 'authorization_code',
                    'client_id' => $config['id'],
                    'client_secret' => $config['secret'],
                    'redirect_uri' => $config['redirect_url'],
                    'code' => $authCode,
                ]
            ])
            ->willReturn(new Response(200, [], '{ "error": "any", "error_description": "Custom Error Message Foo" }'));

        // start server instance with custom http.
        $server = new MevetoServer($http);

        // set config on server.
        $server->config($config);

        try {
            // call access code method.
            $accessToken = $server->accessToken($authCode);
        } catch (ClientErrorException $e) {
            // assert proper message.
            static::assertContains('Custom Error Message Foo', $e->getMessage());
        }
    }

    /**
     * Test config with invalid key.
     *
     * @throws
     */
    public function testConfigWithInvalidKey()
    {
        // start server instance.
        $server = new MevetoServer();

        // invalid config.
        $configWithInvalidKey = [
            'foo' => 'bar'
        ];

        // try setting invalid config.
        try {
            $server->config($configWithInvalidKey);
        } catch (KeyNotValidException $e) {
            // assert proper handling the invalid key.
            static::assertContains(
                'Your `Meveto configuration` array has an unexpected key `foo`.',
                $e->getMessage()
            );
        }
    }

    /**
     * Test config with missing value.
     *
     * @throws
     */
    public function testConfigWithMissingValue()
    {
        // start server instance.
        $server = new MevetoServer();

        // invalid config.
        $configWithMissingValue = [
            'id' => null
        ];

        // try setting config.
        try {
            $server->config($configWithMissingValue);
        } catch (ValueRequiredAtException $e) {
            // assert proper handling the invalid key.
            static::assertContains(
                '`id` is required inside `Meveto configuration` array and it can not be empty or null.',
                $e->getMessage()
            );
        }
    }

    /**
     * Test config with valid data.
     *
     * @throws
     */
    public function testConfigWithValidData()
    {
        // start server instance.
        $server = new MevetoServer();

        // generate some valid config.
        $validConfig = [
            'id'            => 'client-foo',
            'secret'        => 'secret-bar',
            'scope'         => 'some-scope-here',
            'redirect_url'  => 'https://get.meback.com/callback',
            'state'         => 'some-app-state-here',
            'authEndpoint'  => 'https://dashboard.meveto.com/oauth-client/foo/barr',
            'tokenEndpoint' => 'https://prod.meveto.com/oauth/token/foo/bar',
        ];

        // try setting config.
        try {
            $server->config($validConfig);
        } catch (\Exception $e) {
            // should not except anything.
            // but if it does. throw it.
            throw $e;
        }

        // assert proper handling the invalid key.
        static::assertEquals(
            $validConfig,
            $this->getProtectedPropertyValue($server, 'config')
        );
    }

    /**
     * Test config with emtpy data.
     *
     * @throws
     */
    public function testConfigWithEmptyData()
    {
        // start server instance.
        $server = new MevetoServer();

        // empty config.
        $emptyConfig = [];

        // try setting empty config.
        $configSetResponse = $server->config($emptyConfig);

        // assert returned false.
        static::assertFalse($configSetResponse);
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
            // reflect to get property..
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
        return mb_substr(bin2hex(random_bytes($length)), 0, $length);
    }
}
