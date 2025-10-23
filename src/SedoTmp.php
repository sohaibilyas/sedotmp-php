<?php

declare(strict_types=1);

namespace SohaibIlyas\SedoTmp;

final class SedoTmp
{
    private ?string $accessToken = null;

    public function __construct(
        private readonly string $clientId,
        private readonly string $clientSecret,
        private readonly string $baseUrl = 'https://api.sedotmp.com',
        private readonly string $authUrl = 'https://auth.sedotmp.com/oauth/token',
    ) {}

    public function authenticate(): string
    {
        $ch = curl_init($this->authUrl);

        if ($ch === false) {
            throw new \RuntimeException('Failed to initialize cURL');
        }

        $payload = json_encode([
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'audience' => $this->baseUrl.'/',
            'grant_type' => 'client_credentials',
        ]);

        if ($payload === false) {
            throw new \RuntimeException('Failed to encode JSON payload');
        }

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
        ]);

        $response = curl_exec($ch);

        if ($response === false) {
            $error = curl_error($ch);
            curl_close($ch);
            throw new \RuntimeException('cURL error: '.$error);
        }

        if (! is_string($response)) {
            curl_close($ch);
            throw new \RuntimeException('Unexpected response type from cURL');
        }

        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200) {
            throw new \RuntimeException('Failed to authenticate with SedoTMP API');
        }

        $data = json_decode($response, true);

        if (! is_array($data) || ! isset($data['access_token'])) {
            throw new \RuntimeException('No access token in response');
        }

        $this->accessToken = $data['access_token'];

        return $this->accessToken;
    }

    public function getAccessToken(): ?string
    {
        return $this->accessToken;
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
}
