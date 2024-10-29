<?php

namespace EdLugz\Tanda;

use EdLugz\Tanda\Exceptions\TandaRequestException;
use EdLugz\Tanda\Logging\Log;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\ServerException;
use Illuminate\Support\Facades\Cache;

class TandaClient
{
    /**
     * Guzzle client initialization.
     *
     * @var Client
     */
    protected Client $client;

    /**
     * Tanda APIs application client id.
     *
     * @var string
     */
    protected string $clientId;

    /**
     * Tanda APIs application client secret.
     *
     * @var string
     */
    protected string $clientSecret;

    /**
     * Access token generated by Tanda APIs.
     *
     * @var string
     */
    protected string $accessToken;

    /**
     * Base URL end points for the Tanda APIs.
     *
     * @var array
     */
    protected array $base_url;

    /**
     * Make the initializations required to make calls to the Tanda APIs
     * and throw the necessary exception if there are any missing-required
     * configurations.
     *
     * @throws TandaRequestException
     * @throws Exception
     */
    public function __construct()
    {
        $this->validateConfigurations();

        $mode = config('tanda.mode');

        $this->base_url = [
            'uat'  => 'https://tandaio-api-uats.tanda.co.ke',
            'live' => config('tanda.base_url'),
        ];

        $options = [
            'base_uri' => $this->base_url[$mode],
            'verify'   => $mode !== 'uat',
        ];

        if (config('tanda.logs.enabled')) {
            $options = Log::enable($options);
        }

        $this->client = new Client($options);
        $this->clientId = config('tanda.client_id');
        $this->clientSecret = config('tanda.client_secret');
        $this->getAccessToken();
    }

    /**
     * Get access token from Tanda APIs.
     *
     * @return void
     * @throws TandaRequestException
     *
     */
    protected function getAccessToken(): void
    {
        //check if access token exists and not expired
        if (!Cache::get('tanda_token')) {
            // Set the auth option and fetch new token
            $options = [
                'auth' => [
                    $this->clientId,
                    $this->clientSecret,
                ],
                'form_params' => [
                    'grant_type' => 'client_credentials',
                ],
                'headers' => [
                    'Content-Type' => 'application/x-www-form-urlencoded',
                ],
            ];

            $accessTokenDetails = $this->call('accounts/v1/oauth/token', $options);

            //add to Cache
            Cache::add('tanda_token', $accessTokenDetails->access_token, now()->addMinutes(58));

            $this->accessToken = Cache::get('tanda_token');
        } else {
            $this->accessToken = Cache::get('tanda_token');
        }
    }

    /**
     * Validate configurations.
     */
    protected function validateConfigurations(): void
    {
        // Validate credentials
        if (empty(config('tanda.client_id'))) {
            throw new \InvalidArgumentException('client id has not been set.');
        }

        if (empty(config('tanda.client_secret'))) {
            throw new \InvalidArgumentException('client secret has not been set');
        }
    }

    /**
     * Make API calls to Tanda API.
     *
     * @param string $url
     * @param array  $options
     * @param string $method
     *
     * @throws TandaRequestException
     *
     * @return mixed
     */
    protected function call(string $url, array $options = [], string $method = 'POST'): mixed
    {
        if (isset($this->accessToken)) {
            $options['headers'] = ['Authorization' => 'Bearer '.$this->accessToken];
        }

        try {
            $response = $this->client->request($method, $url, $options);

            $stream = $response->getBody();
            $stream->rewind();
            $content = $stream->getContents();

            return json_decode($content);
        } catch (ServerException $e) {
            $response = json_decode($e->getResponse()->getBody()->getContents());
            if (isset($response->Envelope)) {
                $message = 'Tanda APIs: '.$response->Envelope->Body->Fault->faultstring;

                throw new TandaRequestException($message, $e->getCode());
            }

            throw new TandaRequestException('Tanda APIs: '.$e->getMessage(), $e->getCode());
        } catch (ClientException $e) {
            $response = json_decode($e->getResponse()->getBody()->getContents());

            throw new TandaRequestException('Tanda APIs: '.$response->status, $e->getCode());
        } catch (ConnectException $e) {
            // Handle 504 Gateway Timeout
            throw new TandaRequestException('Tanda APIs: Gateway Timeout', 504);
        } catch (GuzzleException $e) {
            throw new TandaRequestException('Tanda APIs: '.$e->getMessage(), $e->getCode());
        }
    }
}
