<?php

declare(strict_types=1);

namespace SohaibIlyas\SedoTmp;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

final class SedoTmp
{
    private ?string $accessToken = null;

    private readonly Client $httpClient;

    public function __construct(
        private readonly string $clientId,
        private readonly string $clientSecret,
        private readonly string $apiVersion = 'v1',
        private readonly string $baseUrl = 'https://api.sedotmp.com',
        private readonly string $authUrl = 'https://auth.sedotmp.com/oauth/token',
        ?Client $httpClient = null,
    ) {
        $this->httpClient = $httpClient ?? new Client;
    }

    public function content(): Content
    {
        return new Content($this);
    }

    public function platform(): Platform
    {
        return new Platform($this);
    }

    public function getHttpClient(): Client
    {
        return $this->httpClient;
    }

    public function setAccessToken(string $token): self
    {
        $this->accessToken = $token;

        return $this;
    }

    public function getAccessToken(): string
    {
        if ($this->accessToken === null) {
            try {
                $response = $this->httpClient->post($this->authUrl, [
                    'json' => [
                        'client_id' => $this->clientId,
                        'client_secret' => $this->clientSecret,
                        'audience' => $this->baseUrl.'/',
                        'grant_type' => 'client_credentials',
                    ],
                ]);

                $data = json_decode($response->getBody()->getContents(), true);

                if (! is_array($data) || ! isset($data['access_token'])) {
                    throw new \RuntimeException('No access token in response');
                }

                $this->accessToken = $data['access_token'];
            } catch (GuzzleException $e) {
                throw new \RuntimeException('Failed to authenticate with SedoTMP API: '.$e->getMessage(), 0, $e);
            }
        }

        return $this->accessToken;
    }

    public function hasAccessToken(): bool
    {
        return $this->accessToken !== null;
    }

    public function getClientId(): string
    {
        return $this->clientId;
    }

    public function getClientSecret(): string
    {
        return $this->clientSecret;
    }

    public function getBaseUrl(): string
    {
        return $this->baseUrl;
    }

    public function getAuthUrl(): string
    {
        return $this->authUrl;
    }

    public function getApiVersion(): string
    {
        return $this->apiVersion;
    }
}
