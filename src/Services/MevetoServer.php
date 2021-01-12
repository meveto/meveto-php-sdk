<?php

namespace Meveto\Client\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use Meveto\Client\Exceptions\Http;
use Meveto\Client\Exceptions\InvalidClient;
use Meveto\Client\Exceptions\InvalidConfig;
use Meveto\Client\Exceptions\Validation;

/**
 * Class MevetoServer.
 */
class MevetoServer
{
    /**
     * @var array
     */
    protected $architectures = [
        'web',
        'rest',
    ];

    /**
     * @var string
     */
    protected $architecture;

    /**
     * @var int
     */
    protected $stateLength = 128;

    /**
     * @var array
     */
    protected $config = [
        'id'    => '',
        'secret'      => '',
        'scope'  => 'default-client-access',
        'redirect_url' => '',
        'state' => '',
        'authEndpoint' => 'https://dashboard.meveto.com/oauth-client',
        'tokenEndpoint' => 'https://prod.meveto.com/oauth/token',
    ];

    /**
     * @var array
     */
    protected $requiredConfig = [
        'id',
        'secret',
        'redirect_url',
        'authEndpoint',
        'tokenEndpoint',
    ];

    /**
     * @var Client
     */
    protected $http;

    /**
     * @var string
     */
    protected $resourceEndpoint;

    /**
     * @var string
     */
    protected $aliasEndpoint;

    /**
     * @var string
     */
    protected $eventUserEndpoint;

    /**
     * MevetoServer constructor.
     *
     * @param Client|null $client
     */
    public function __construct(Client $client = null)
    {
        // assign new client instance, if none.
        $this->http = $client ?: new Client();
    }

    /**
     * Set the architecture of your application for Meveto
     *
     * @param string $architecture
     *
     * @throws InvalidConfig
     *
     * @return void
     */
    public function architecture(string $architecture): void
    {
        if (! in_array($architecture, $this->architectures, true)) {
            throw InvalidConfig::architectureNotSupported($architecture, $this->architectures);
        }

        $this->architecture = $architecture;
    }

    /**
     * Set Meveto configuration information
     *
     * @param array $config The Meveto configuration array
     *
     * @throws
     *
     * @return bool
     *
     */
    public function config(array $config): bool
    {
        if (empty($config)) {
            return false;
        }

        foreach ($config as $key => $value) {
            $value = ! is_array($value) ? trim($value) : $value;

            if (! array_key_exists($key, $this->config)) {
                throw InvalidConfig::keyNotValid('Meveto configuration', $key);
            }

            if (empty($value) && in_array($key, $this->requiredConfig, true)) {
                throw InvalidConfig::valueRequiredAt('Meveto configuration', $key);
            }

            $this->config[$key] = $value;
        }

        return true;
    }

    /**
     * Set state for the current request
     *
     * @param string $state
     *
     * @throws Validation
     *
     * @return void
     */
    public function state(string $state): void
    {
        $state = trim($state);

        // throw if empty state.
        if (empty($state)) {
            throw Validation::stateRequired();
        }

        // throw if too short state size.
        if (mb_strlen($state) < $this->stateLength) {
            throw Validation::stateTooShort((string) $this->stateLength);
        }

        // assign on config.
        $this->config['state'] = $state;
    }

    /**
     * Set the resource owner endpoint
     *
     * @param string $api_url
     *
     * @return void
     */
    public function resourceEndpoint(string $api_url): void
    {
        $this->resourceEndpoint = $api_url;
    }

    /**
     * Set the alias endpoint
     *
     * @param string $api_url
     *
     * @return void
     */
    public function aliasEndpoint(string $api_url): void
    {
        $this->aliasEndpoint = $api_url;
    }

    /**
     * Set the endpoint for exchanging user token for user identifier that's associated with an event
     *
     * @param string $api_url
     *
     * @return void
     */
    public function eventUserEndpoint(string $api_url): void
    {
        $this->eventUserEndpoint = $api_url;
    }

    /**
     * Process a `login with Meveto` request to a client application
     *
     * @param string|null $clientToken Meveto login token
     * @param string|null $sharingToken An account sharing token
     *
     * @throws InvalidConfig
     *
     * @return string The Authorization URL
     *
     */
    public function processLogin(string $clientToken = null, string $sharingToken = null): string
    {
        if (! empty($this->config['state'])) {
            $query = [
                'client_id' => $this->config['id'],
                'scope' => $this->config['scope'],
                'response_type' => 'code',
                'redirect_uri' =>  $this->config['redirect_url'],
                'state' => $this->config['state'],
            ];

            if ($clientToken !== null && $clientToken !== '') {
                $query['client_token'] = $clientToken;
            }

            if ($sharingToken !== null && $sharingToken !== '') {
                $query['sharing_token'] = $sharingToken;
            }

            $authorize_query = http_build_query($query);

            return $this->config['authEndpoint'] . '?' . $authorize_query;
        }

        throw InvalidConfig::stateNotSet();
    }

    /**
     * Exchange authorization code with an access token
     *
     * @param string $authCode The authentication code
     *
     * @throws-variation clientNotFound
     * @throws-variation clientError
     *
     * @throws InvalidClient
     * @throws GuzzleException
     *
     * @return array
     */
    public function accessToken(string $authCode): array
    {
        $response = $this->http->post($this->config['tokenEndpoint'], [
            'form_params' => [
                'grant_type' => 'authorization_code',
                'client_id' => $this->config['id'],
                'client_secret' => $this->config['secret'],
                'redirect_uri' => $this->config['redirect_url'],
                'code' => $authCode,
            ],
        ]);

        $content = json_decode((string) $response->getBody(), true);

        if (isset($content['error'])) {
            if ($content['error'] === 'invalid_client') {
                throw InvalidClient::clientNotFound();
            }

            throw InvalidClient::clientError($content['error_description']);
        }

        return $content;
    }

    /**
     * Use access token to get resource owner information
     *
     * @param string $token The access token
     *
     * @return array The resource owner information
     *
     * @throws-variation notAuthorized
     * @throws-variation notAuthenticated
     * @throws-variation clientError
     *
     * @throws ClientException
     * @throws InvalidClient
     * @throws Http
     * @throws GuzzleException
     */
    public function resourceOwnerData(string $token): array
    {
        try {
            $response = $this->http->get($this->resourceEndpoint, [
                'query' => [
                    'client_id' => $this->config['id'],
                ],
                'headers' => [
                    'Accept'     => 'application/json',
                'Authorization' => 'Bearer '.$token,
                ],
            ]);
        } catch (ClientException $e) {
            throw $e;
        }
        if ($response->getStatusCode() === 401) {
            throw Http::notAuthenticated();
        }
        if ($response->getStatusCode() === 403) {
            throw Http::notAuthorized();
        }

        $content = json_decode((string) $response->getBody(), true);

        if (isset($content['error'])) {
            throw InvalidClient::clientError($content['error_description']);
        }

        if (! isset($content['payload'])) {
            throw InvalidClient::clientError('Empty payload');
        }

        return $content['payload'];
    }

    /**
     * Synchronize access token owner's Meveto identifier with a local identifier of the user
     *
     * @param string $token The access token
     * @param string $user The local user identifier
     *
     * @throws-variation notAuthorized
     * @throws-variation clientError
     * @throws-variation notAuthenticated
     *
     * @throws InvalidClient
     * @throws Validation
     * @throws GuzzleException
     * @throws Http
     *
     * @return bool True if synchronization is successful false otherwise
     */
    public function synchronizeUserID(string $token, string $user): bool
    {
        $response = $this->http->post($this->aliasEndpoint, [
            'form_params' => [
                'client_id' => $this->config['id'],
                'alias_name' => $user,
            ],
            'headers' => [
                'Accept'     => 'application/json',
                'Authorization' => 'Bearer '.$token,
            ],
        ]);

        if ($response->getStatusCode() === 401) {
            throw Http::notAuthenticated();
        }
        if ($response->getStatusCode() === 403) {
            throw Http::notAuthorized();
        }

        $content = json_decode((string) $response->getBody(), true);

        if (isset($content['error'])) {
            throw InvalidClient::clientError($content['error_description']);
        }

        if ($content['status'] === 'Alias_Added_Successfully') {
            return true;
        }

        if ($content['status'] === 'Input_Data_Validation_Failed') {
            throw Validation::inputDataInvalid($content['errors']);
        }

        return false;
    }

    /**
     * Retrieve identifier for the user token.
     *
     * @param string $userToken The access token
     *
     * @throws InvalidClient
     * @throws GuzzleException
     * @throws ClientException
     *
     * @return string
     *
     * @throws-variation clientError
     *
     */
    public function tokenUser(string $userToken): string
    {
        try {
            $response = $this->http->get($this->eventUserEndpoint, [
                'query' => [
                    'token' => $userToken,
                ],
                'headers' => [
                    'Accept'     => 'application/json',
                ],
            ]);
        } catch (ClientException $e) {
            throw $e;
        }

        $content = json_decode((string) $response->getBody(), true);

        if (isset($content['error'])) {
            throw InvalidClient::clientError($content['error_description']);
        }

        if ($content['status'] !== 'Token_User_Retrieved') {
            throw InvalidClient::clientError('Error retrieving token.');
        }

        if ($content['status'] === 'Invalid_User_Token') {
            throw InvalidClient::clientError($content['message']);
        }

        return $content['payload']['user'];
    }
}
