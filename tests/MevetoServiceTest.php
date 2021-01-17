<?php

namespace Tests;

use Meveto\Client\Enum\Architecture;
use Meveto\Client\Exceptions\InvalidConfig\ArchitectureNotSupportedException;
use Meveto\Client\Exceptions\InvalidConfig\ConfigNotSetException;
use Meveto\Client\Exceptions\Validation\KeyNotValidException;
use Meveto\Client\Exceptions\Validation\ValueRequiredAtException;
use Meveto\Client\MevetoService;
use Meveto\Client\Services\MevetoServer;

/**
 * Class MevetoServiceTest.
 */
class MevetoServiceTest extends MevetoTestCase
{
    public function testConstructor()
    {
        $config = [
            'id' => 123
        ];

        $server = $this->createMock(MevetoServer::class);

        $server->expects(static::once())
            ->method('architecture')
            ->with(Architecture::REST);

        $server->expects(static::once())
            ->method('config')
            ->with($config);

        $server->expects(static::once())
            ->method('resourceEndpoint')
            ->with('https://prod.meveto.com/api/client/user');

        $server->expects(static::once())
            ->method('aliasEndpoint')
            ->with('https://prod.meveto.com/api/client/user/alias');

        $server->expects(static::once())
            ->method('eventUserEndpoint')
            ->with('https://prod.meveto.com/api/client/user-for-token');

        try {
            $service = new MevetoService($config, Architecture::REST, $server);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function testFailsOnEmptyConfig()
    {
        $config = [];

        $server = $this->createMock(MevetoServer::class);

        $server->expects(static::once())
            ->method('config')
            ->with($config)
            ->willReturn(false);

        try {
            $service = new MevetoService($config, Architecture::REST, $server);
            $service->getTokenUser('fooo');
        } catch (ConfigNotSetException $e) {
            static::assertStringHasString('Your Meveto client configuration is not set', $e->getMessage());
        }
    }

    public function testGetUserToken()
    {
        $config = [ 'id' ];
        $userToken = 'abc-user-token';

        $server = $this->createMock(MevetoServer::class);

        $server->expects(static::once())
            ->method('config')
            ->with($config)
            ->willReturn(true);

        $server->expects(static::once())
            ->method('tokenUser')
            ->with($userToken)
            ->willReturn('payload-user');

        $service = new MevetoService($config, Architecture::REST, $server);
        $user = $service->getTokenUser($userToken);
        static::assertEquals('payload-user', $user);
    }

    public function testLogin()
    {
        $config = [ 'id' ];
        $clientToken = 'abc-client-token';
        $sharingToken = 'abc-sharing-token';

        $server = $this->createMock(MevetoServer::class);

        $server->expects(static::once())
            ->method('config')
            ->with($config)
            ->willReturn(true);

        $server->expects(static::once())
            ->method('processLogin')
            ->with($clientToken, $sharingToken)
            ->willReturn('https://send.to.login');

        $service = new MevetoService($config, Architecture::REST, $server);
        $url = $service->login($clientToken, $sharingToken);

        static::assertEquals('https://send.to.login', $url);
    }

    public function testSetState()
    {
        $config = [ 'id' ];
        $state = 'abc-state';

        $server = $this->createMock(MevetoServer::class);

        $server->expects(static::once())
            ->method('config')
            ->with($config)
            ->willReturn(true);

        $server->expects(static::once())
            ->method('state')
            ->with($state)
            ->willReturn('https://send.to.login');

        $service = new MevetoService($config, Architecture::REST, $server);
        $service->setState($state);
    }

    public function testGetAccessToken()
    {
        $config = [ 'id' ];
        $authCode = 'abc-auth-code';

        $server = $this->createMock(MevetoServer::class);

        $server->expects(static::once())
            ->method('config')
            ->with($config)
            ->willReturn(true);

        $server->expects(static::once())
            ->method('accessToken')
            ->with($authCode)
            ->willReturn([ 'token' => 'cool-token' ]);

        $service = new MevetoService($config, Architecture::REST, $server);

        $accessToken = $service->getAccessToken($authCode);

        static::assertEquals([ 'token' => 'cool-token' ], $accessToken);
    }

    public function testConnectToMeveto()
    {
        $config = [ 'id' ];
        $token = 'foo';
        $user = 'bar';

        $server = $this->createMock(MevetoServer::class);

        $server->expects(static::once())
            ->method('config')
            ->with($config)
            ->willReturn(true);

        $server->expects(static::once())
            ->method('synchronizeUserID')
            ->with($token, $user)
            ->willReturn(true);

        $service = new MevetoService($config, Architecture::REST, $server);

        $connected = $service->connectToMeveto($token, $user);

        static::assertTrue($connected);
    }

    public function testResourceOwnerData()
    {
        $config = [ 'id' ];
        $token = 'foo';

        $server = $this->createMock(MevetoServer::class);

        $server->expects(static::once())
            ->method('config')
            ->with($config)
            ->willReturn(true);

        $server->expects(static::once())
            ->method('resourceOwnerData')
            ->with($token)
            ->willReturn([ 'some-info' ]);

        $service = new MevetoService($config, Architecture::REST, $server);

        $ownerData = $service->getResourceOwnerData($token);

        static::assertEquals([ 'some-info' ], $ownerData);
    }

    /**
     * Get a protected/private value from a given server instance.
     *
     * @param MevetoService $service
     * @param string $propertyName
     *
     * @return mixed
     */
    protected function getProtectedPropertyValue(MevetoService $service, string $propertyName)
    {
        try {
            // reflect to get property..
            $reflection = new \ReflectionClass($service);
            $property = $reflection->getProperty($propertyName);
            $property->setAccessible(true);

            return $property->getValue($service);
        } catch (\Exception $e) {
            return null;
        }
    }
}
