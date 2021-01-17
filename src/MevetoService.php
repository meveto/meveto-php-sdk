<?php

namespace Meveto\Client;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use Meveto\Client\Exceptions\Http\NotAuthenticatedException;
use Meveto\Client\Exceptions\Http\NotAuthorizedException;
use Meveto\Client\Exceptions\InvalidClient\ClientErrorException;
use Meveto\Client\Exceptions\InvalidClient\ClientNotFoundException;
use Meveto\Client\Exceptions\InvalidConfig\ArchitectureNotSupportedException;
use Meveto\Client\Exceptions\InvalidConfig\ConfigNotSetException;
use Meveto\Client\Exceptions\InvalidConfig\StateNotSetException;
use Meveto\Client\Exceptions\Validation\InputDataInvalidException;
use Meveto\Client\Exceptions\Validation\KeyNotValidException;
use Meveto\Client\Exceptions\Validation\StateRequiredException;
use Meveto\Client\Exceptions\Validation\StateTooShortException;
use Meveto\Client\Exceptions\Validation\ValueRequiredAtException;
use Meveto\Client\Services\MevetoServer;

class MevetoService
{
    /**
     * @var MevetoServer
     */
    protected $MevetoServer;

    /**
     * @var bool
     */
    protected $isConfigSet = false;

    /**
     * @param array $config The Meveto configuration array
     * @param string $architecture The architecture of your application. It is set to web by default
     *
     * @throws ArchitectureNotSupportedException
     * @throws KeyNotValidException
     * @throws ValueRequiredAtException
     */
    public function __construct(array $config, string $architecture = 'web')
    {
        // start server instance.
        $this->MevetoServer = new MevetoServer();

        // set arch.
        $this->setArchitecture($architecture);
        // set config.
        $this->setConfig($config);

        // set urls.
        $this->setResourceEndpoint('https://prod.meveto.com/api/client/user');
        $this->setAliasEndpoint('https://prod.meveto.com/api/client/user/alias');
        $this->setUserEndpoint('https://prod.meveto.com/api/client/user-for-token');
    }

    /**
     * Set the architecture of the client application that is using Meveto
     *
     * @param string $arch Name of the architecture. Accepted values are ['web', 'rest']
     *
     * @throws Exceptions\InvalidConfig\ArchitectureNotSupportedException
     *
     * @return void
     *
     */
    protected function setArchitecture(string $arch)
    {
        $this->MevetoServer->architecture($arch);
    }

    /**
     * Set configuration for Meveto.
     *
     * @param array $config The Meveto configuration array
     *
     * @throws KeyNotValidException
     * @throws ValueRequiredAtException
     *
     * @return void
     *
     */
    protected function setConfig(array $config)
    {
        $this->isConfigSet = $this->MevetoServer->config($config);
    }

    /**
     * Set the Meveto server endpoint to retrieve the resource owner info
     *
     * @param string $api_url The endpoint
     *
     * @return void
     */
    protected function setResourceEndpoint(string $api_url)
    {
        $this->MevetoServer->resourceEndpoint($api_url);
    }

    /**
     * Set Meveto's alias endpoint that can be used to synchronize Meveto and local user identifiers in case these are different.
     *
     * @param string $api_url The endpoint
     *
     * @return void
     */
    protected function setAliasEndpoint(string $api_url)
    {
        $this->MevetoServer->aliasEndpoint($api_url);
    }

    /**
     * Set endpoint for the exchange of a user token with a user identifier when a user action creates an event
     *
     * @param string $api_url The endpoint
     *
     * @return void
     */
    protected function setUserEndpoint(string $api_url)
    {
        $this->MevetoServer->eventUserEndpoint($api_url);
    }

    /**
     * Set the architecture of the client application that is using Meveto
     *
     * @param string $state Name of the architecture. Accepted values are ['web', 'rest']
     *
     * @throws StateRequiredException
     * @throws StateTooShortException
     *
     * @return void
     *
     */
    public function setState(string $state)
    {
        $this->MevetoServer->state($state);
    }

    /**
     * Login to a client application with Meveto
     *
     * @param string|null $clientToken A one time client specific Meveto login token
     * @param string|null $sharingToken An account sharing token
     *
     * @throws StateNotSetException
     *
     * @return string The Authorization URL. Your application should redirect user to this URL
     */
    public function login(string $clientToken = null, string $sharingToken = null): string
    {
        $this->validateRequestdata();

        return $this->MevetoServer->processLogin($clientToken, $sharingToken);
    }

    /**
     * Exchange your Meveto authorization code for an access token. Get the authorization code from your application's redirect URL
     *
     * @param string $authCode The Meveto authorization code
     *
     * @throws ClientNotFoundException
     * @throws ClientErrorException
     * @throws GuzzleException
     *
     * @return array The array contains access_token, refresh_token and expires_in that indicates when the access token will expire.
     *
     */
    public function getAccessToken(string $authCode): array
    {
        $this->validateRequestdata();

        return $this->MevetoServer->accessToken($authCode);
    }

    /**
     * Get resource owner's data as per the specified scope using a Meveto access token
     *
     * @param string $token The Meveto access token
     *
     * @throws ClientException
     * @throws NotAuthenticatedException
     * @throws NotAuthorizedException
     * @throws ClientErrorException
     * @throws GuzzleException
     *
     * @return array The resource owner information
     *
     */
    public function getResourceOwnerData(string $token): array
    {
        $this->validateRequestdata();

        return $this->MevetoServer->resourceOwnerData($token);
    }

    /**
     * Synchronize a local user identifier with a Meveto user identifier
     *
     * @param string $token The Meveto access token
     * @param string $user A local (at your app) user identifier that is to be synchronized to a Meveto identifier
     *
     * @return bool True if synchronization is successful false otherwise
     *
     * @throws NotAuthenticatedException
     * @throws NotAuthorizedException
     * @throws ClientErrorException
     * @throws InputDataInvalidException
     * @throws ConfigNotSetException
     * @throws GuzzleException
     */
    public function connectToMeveto(string $token, string $user): bool
    {
        $this->validateRequestdata();

        return $this->MevetoServer->synchronizeUserID($token, $user);
    }

    /**
     * Get user identifier for a valid user token.
     *
     * @param string $userToken The user token your application's webhook received from Meveto
     *
     * @throws GuzzleException
     * @throws ClientErrorException
     *
     * @return string The user identifier
     *
     */
    public function getTokenUser(string $userToken): string
    {
        $this->validateRequestdata();

        return $this->MevetoServer->tokenUser($userToken);
    }

    /**
     * Validate that Meveto configuration and database credentials have been set
     *
     * @throws ConfigNotSetException
     */
    protected function validateRequestData()
    {
        if (! $this->isConfigSet) {
            throw new ConfigNotSetException('');
        }
    }
}
