<?php

namespace Meveto\Client;

use Meveto\Client\Exceptions\InvalidConfig;
use Meveto\Client\Services\MevetoServer;

class MevetoService
{
    /** @var Meveto\Client\Services\MevetoServer */
    protected $MevetoServer;

    /** @var Meveto\Client\Services\Database */
    protected $database;

    /** @var */
    protected $isConfigSet = false;

    /** @var */
    protected $isDatabaseSet = false;

    /**
     * @param array $config The Meveto configuration array
     * @param array $database The database credentials array for your application. The SDK will use these credentials to communicate
     * to communicate with your database. The Meveto servers do not need these credentials at all and hence these will never be 
     * sent any where.
     * @param string $architecture The architecture of your application. It is set to web by default
     * 
     * @return void
     */
    public function __construct(array $config, string $architecture = 'web')
    {
        $this->MevetoServer = new MevetoServer();
        $this->setArchitecture($architecture);
        $this->setConfig($config);
        $this->setResourceEndpoint("https://prod.meveto.com/api/client/user");
        $this->setAliasEndpoint("https://prod.meveto.com/api/client/user/alias");        
        $this->setUserEndpoint("https://prod.meveto.com/api/client/user-for-token");
    }

    /**
     * Set the architecture of the client application that is using Meveto
     * 
     * @param string $arch Name of the architecture. Accepted values are ['web', 'rest']
     * @return void
     * 
     * @throws architectureNotSupported
     */
    protected function setArchitecture(string $arch): void
    {
        $this->MevetoServer->architecture($arch);
    }

    /**
     * Set configuration for Meveto.
     * 
     * @param array $config The Meveto configuration array
     * @return void
     * 
     * @throws keyNotValid
     * @throws valueRequiredAt
     */
    protected function setConfig(array $config): void
    {
        $this->isConfigSet = $this->MevetoServer->config($config);
    }

    /**
     * Set the Meveto server endpoint to retrieve the resource owner info
     * 
     * @param string $api_url The endpoint
     * @return void
     */
    protected function setResourceEndpoint(string $api_url): void
    {
        $this->MevetoServer->resourceEndpoint($api_url);
    }

    /**
     * Set Meveto's alias endpoint that can be used to synchronize Meveto and local user identifiers in case these are different.
     * 
     * @param string $api_url The endpoint
     * @return void
     */
    protected function setAliasEndpoint(string $api_url): void
    {
        $this->MevetoServer->aliasEndpoint($api_url);
    }

    /**
     * Set endpoint for the exchange of a user token with a user identifier when a user action creates an event
     * 
     * @param string $api_url The endpoint
     * @return void
     */
    protected function setUserEndpoint(string $api_url): void
    {
        $this->MevetoServer->eventUserEndpoint($api_url);
    }

    /**
     * Set the architecture of the client application that is using Meveto
     * 
     * @param string $state Name of the architecture. Accepted values are ['web', 'rest']
     * @return void
     * 
     * @throws architectureNotSupported
     */
    public function setState(string $state): void
    {
        $this->MevetoServer->state($state);
    }

    /**
     * Login to a client application with Meveto
     * 
     * @param string $clientToken A one time client specific Meveto login token
     * @param string $sharingToken An account sharing token
     * @return string The Authorization URL. Your application should redirect user to this URL
     * 
     * @throws configNotSet
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
     * @return array The array contains access_token, refresh_token and expires_in that indicates when the access token will expire.
     * 
     * @throws configNotSet
     * @throws clientNotFound
     * @throws clientError
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
     * @return array The resource owner information
     * 
     * @throws configNotSet
     * @throws notAuthenticated
     * @throws notAuthorized
     * @throws clientError
     * @throws GuzzleHttp\Exception\ClientException
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
     * @return bool True if synchronization is successful false otherwise
     * 
     * @throws configNotSet
     * @throws notAuthenticated
     * @throws notAuthorized
     * @throws clientError
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
     * @return string The user identifier
     * 
     * @throws clientError
     * @throws GuzzleHttp\Exception\ClientException
     */
    public function getTokenUser(string $userToken): string
    {
        $this->validateRequestdata();
        return $this->MevetoServer->tokenUser($userToken);
    }

    /**
     * Validate that Meveto configuration and database credentials have been set
     * 
     * @throws configNotSet
     */
    protected function validateRequestdata(): void
    {
        if (!$this->isConfigSet)
        {
            throw InvalidConfig::configNotSet();
        }
    }

}