<?php

declare(strict_types=1);

namespace SohaibIlyas\SedoTmp;

use GuzzleHttp\Exception\GuzzleException;

final readonly class Platform
{
    public function __construct(
        private SedoTmp $client,
    ) {}

    /**
     * @return array<int|string, mixed>
     */
    public function getContentCampaigns(int $page): array
    {
        $accessToken = $this->client->getAccessToken();

        if ($accessToken === null) {
            throw new \RuntimeException('Client is not authenticated. Call authenticate() first.');
        }

        $url = $this->client->getBaseUrl().'/platform/'.$this->client->getApiVersion().'/content-campaigns?page='.$page;

        try {
            $response = $this->client->getHttpClient()->get($url, [
                'headers' => [
                    'Authorization' => 'Bearer '.$accessToken,
                    'Content-Type' => 'application/json',
                ],
            ]);

            $data = json_decode($response->getBody()->getContents(), true);

            if (! is_array($data)) {
                throw new \RuntimeException('Invalid response format from API');
            }

            return $data;
        } catch (GuzzleException $e) {
            throw new \RuntimeException('Failed to fetch content campaigns from SedoTMP API: '.$e->getMessage(), 0, $e);
        }
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<int|string, mixed>
     */
    public function createContentCampaign(array $data): array
    {
        $accessToken = $this->client->getAccessToken();

        if ($accessToken === null) {
            throw new \RuntimeException('Client is not authenticated. Call authenticate() first.');
        }

        $url = $this->client->getBaseUrl().'/platform/'.$this->client->getApiVersion().'/content-campaigns';

        try {
            $response = $this->client->getHttpClient()->post($url, [
                'headers' => [
                    'Authorization' => 'Bearer '.$accessToken,
                    'Content-Type' => 'application/json',
                ],
                'json' => $data,
            ]);

            $responseData = json_decode($response->getBody()->getContents(), true);

            if (! is_array($responseData)) {
                throw new \RuntimeException('Invalid response format from API');
            }

            return $responseData;
        } catch (GuzzleException $e) {
            throw new \RuntimeException('Failed to create content campaign in SedoTMP API: '.$e->getMessage(), 0, $e);
        }
    }
}
