<?php
/**
 * @copyright Grovo Learning, Inc.
 */

namespace Grovo\Api\Client;

use Grovo\Api\Client\Contexts\Users;
use GuzzleHttp\Client as HttpClient;

/**
 * Class GrovoApi
 *
 * @package Grovo\Api
 * @subpackage Client
 * @version 1.0
 * @author Yitzchok Willroth (yitz@grovo.com)
 * @final
 */
final class GrovoApi
{
    use Users;

    private $apiUrl = 'https://api-sandbox.grovo.com';
    private $authUrl= 'https://auth-sandbox.grovo.com';
    private $httpClient;
    private $clientId;
    private $clientSecret;
    private $accessToken;
    private $onUpdateAccessTokenCallback;

    /**
     * @param string $clientId
     * @param string $clientSecret
     * @param mixed $accessToken
     * @param mixed $onUpdateAccessTokenCallback
     */
    public function __construct($clientId, $clientSecret, $accessToken, $onUpdateAccessTokenCallback = null)
    {
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;

        if (is_callable($accessToken))
        {
            $onUpdateAccessTokenCallback = $accessToken;
            $accessToken = null;
        }

        $this->accessToken = $accessToken;
        $this->onUpdateAccessTokenCallback = $onUpdateAccessTokenCallback;

        $this->httpClient = new HttpClient;
    }

    /**
     * @param $httpClient
     */
    public function setHttpClient($httpClient)
    {
        $this->httpClient = $httpClient;
    }

    /**
     * @param string $url
     */
    public function setApiUrl($url)
    {
        $this->apiUrl = $url;
    }

    /**
     * @param string $url
     */
    public function setAuthUrl($url)
    {
        $this->authUrl = $url;
    }

    /**
     *
     */
    private function renewAccessToken()
    {
        $this->accessToken = $this->requestAccessToken();

        if ($this->onUpdateAccessTokenCallback)
        {
            $this->onUpdateAccessTokenCallback->__invoke($this->accessToken);
        }
    }

    /**
     * @return string
     */
    private function requestAccessToken()
    {
        $response = $this->httpClient->post($this->authUrl . '/token', [
            'body' => json_encode([
                'grant_type' => 'client_credentials',
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecret
            ], JSON_PRETTY_PRINT),
            'headers' => [
                'Content-Type' => 'application/json'
            ]
        ]);

        $responseJsonApi = json_decode($response->getBody(), true);

        return (string) $responseJsonApi['access_token'];
    }

    /**
     * @param string $url
     * @return string
     */
    private function get($url)
    {
        $response = $this->sendGetRequest($url);

        if ($this->detectUnauthorizedResponse($response))
        {
            $this->renewAccessToken();
            $response = $this->sendGetRequest($url);
        }

        return (string) $response->getBody();
    }

    /**
     * @param string $url
     * @param array $attributes
     * @return string
     */
    private function post($url, array $attributes)
    {
        $response = $this->sendPostRequest($url, $attributes);

        if ($this->detectUnauthorizedResponse($response))
        {
            $this->renewAccessToken();
            $response = $this->sendPostRequest($url, $attributes);
        }

        return (string) $response->getBody();
    }

    /**
     * @param string $url
     * @param array $attributes
     * @return string
     */
    private function patch($url, array $attributes)
    {
        $response = $this->sendPatchRequest($url, $attributes);

        if ($this->detectUnauthorizedResponse($response))
        {
            $this->renewAccessToken();
            $response = $this->sendPatchRequest($url, $attributes);
        }

        return (string) $response->getBody();
    }

    /**
     * @param string $url
     * @return boolean
     */
    private function delete($url)
    {
        $response = $this->sendDeleteRequest($url);

        if ($this->detectUnauthorizedResponse($response))
        {
            $this->renewAccessToken();
            $response = $this->sendDeleteRequest($url);
        }

        return $response->getStatusCode() == 204;
    }

    /**
     * @param string $url
     * @return
     */
    private function sendGetRequest($url)
    {
        return $this->httpClient->get($this->apiUrl . $url, [
            'headers' => $this->prepareHeaders(),
            'exceptions' => false,
        ]);
    }

    /**
     * @param string $url
     * @param array $attributes
     * @return
     */
    private function sendPostRequest($url, array $attributes)
    {
        return $this->httpClient->post($this->apiUrl . $url, [
            'headers' => $this->prepareHeaders(),
            'body' => json_encode([
                'data' => [
                    'attributes' => $attributes
                ]
            ], JSON_PRETTY_PRINT),
            'exceptions' => false,
        ]);
    }

    /**
     * @param string $url
     * @return
     */
    private function sendDeleteRequest($url)
    {
        return $this->httpClient->delete($this->apiUrl . $url, [
            'headers' => $this->prepareHeaders(),
            'exceptions' => false,
        ]);
    }

    /**
     * @param $url
     * @param $attributes
     * @return
     */
    private function sendPatchRequest($url, array $attributes)
    {
        $options = [
            'headers' => $this->prepareHeaders(),
            'body' => json_encode([
                'data' => [
                    'attributes' => $attributes
                ]
            ], JSON_PRETTY_PRINT),
            'exceptions'=> false
        ];

        return $this->httpClient->patch($this->apiUrl . $url, $options);
    }

    /**
     * @return array
     */
    private function prepareHeaders()
    {
        $headers = [
            'Content-Type' => 'application/json'
        ];

        if ($this->accessToken)
        {
            $headers['Authorization'] = 'Bearer ' . $this->accessToken;
        }

        return $headers;
    }

    /**
     * @param $response
     * @return boolean
     */
    private function detectUnauthorizedResponse($response)
    {
        return $response->getStatusCode() == 401;
    }

}
