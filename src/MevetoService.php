<?php

namespace Meveto\Client;

use Meveto\Client\Exceptions\Database as ExceptionsDatabase;
use Meveto\Client\Exceptions\InvalidConfig;
use Meveto\Client\Services\MevetoServer;
use Meveto\Client\Services\Database;

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
    public function __construct(array $config, array $database, string $architecture = 'web')
    {
        $this->MevetoServer = MevetoServer::class;
        $this->database = Database::class;
        $this->setArchitecture($architecture);
        $this->setConfig($config);
        $this->setDatabase($database);
        $this->setResourceEndpoint("https://prod.meveto.com/api/user");
        $this->setAliasEndpoint("https://prod.meveto.com/api/user/alias");
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
     * Set your database credentials.
     * 
     * @param array $credentials The credentials that will be used by this library to communicate with your database
     * @return void
     * 
     * @throws keyNotValid
     * @throws driverNotSupported
     * @throws valueRequiredAt
     */
    protected function setDatabase(array $credentials): void
    {
        $this->isDatabaseSet = $this->database->credentials($credentials);
        $this->database->establishConnection();
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
     * Login to a client application with Meveto
     * 
     * @return void
     * 
     * @throws configNotSet
     * @throws databaseNotSet
     */
    public function login(): void
    {
        $this->validateRequestdata();
        $this->MevetoServer->processLogin();
    }

    /**
     * Exchange your Meveto authorization code for an access token. Get the authorization code from your application's redirect URL
     * 
     * @param string $authCode The Meveto authorization code
     * @return array The array contains access_token, refresh_token and expires_in that indicates when the access token will expire.
     * 
     * @throws configNotSet
     * @throws databaseNotSet
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
     * @throws databaseNotSet
     * @throws notAuthenticated
     * @throws notAuthorized
     * @throws clientError
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
     * @throws databaseNotSet
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
     * Let Meveto SDK know when your application successfully logs a user in using Meveto.
     * 
     * @param string $user The user identifier that was used to login the user
     * @return void
     * 
     * @throws configNotSet
     * @throws databaseNotSet
     * @throws databaseError
     */
    public function userLoggedIn(string $user): void
    {
        $this->validateRequestdata();
        $this->database->loginUser($user);
    }

    /**
     * Let Meveto SDK know when your application logs a user out.
     * 
     * @param string $user The user identifier for the user that logged out
     * @return void
     * 
     * @throws configNotSet
     * @throws databaseNotSet
     * @throws databaseError
     * @throws userNotFound
     */
    public function userLoggedOut(string $user): void
    {
        $this->validateRequestdata();
        $this->database->logoutUser($user);
    }

    /**
     * Validate that Meveto configuration and database credentials have been set
     * 
     * @throws configNotSet
     * @throws databaseNotSet
     */
    protected function validateRequestdata(): void
    {
        if (!$this->isConfigSet)
        {
            throw InvalidConfig::configNotSet();
        }
        
        if(!$this->isDatabaseSet)
        {
            throw ExceptionsDatabase::databaseNotSet();
        }
    }

}