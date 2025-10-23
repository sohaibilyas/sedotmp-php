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
        $url = $this->client->getBaseUrl().'/platform/'.$this->client->getApiVersion().'/content-campaigns?page='.$page;

        try {
            $response = $this->client->getHttpClient()->get($url, [
                'headers' => [
                    'Authorization' => 'Bearer '.$this->client->getAccessToken(),
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
     * @return array<int|string, mixed>
     */
    public function getContentCampaign(string $id): array
    {
        $url = $this->client->getBaseUrl().'/platform/'.$this->client->getApiVersion().'/content-campaigns/'.$id;

        try {
            $response = $this->client->getHttpClient()->get($url, [
                'headers' => [
                    'Authorization' => 'Bearer '.$this->client->getAccessToken(),
                    'Content-Type' => 'application/json',
                ],
            ]);

            $data = json_decode($response->getBody()->getContents(), true);

            if (! is_array($data)) {
                throw new \RuntimeException('Invalid response format from API');
            }

            return $data;
        } catch (GuzzleException $e) {
            throw new \RuntimeException('Failed to fetch content campaign from SedoTMP API: '.$e->getMessage(), 0, $e);
        }
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<int|string, mixed>
     */
    public function createContentCampaign(array $data): array
    {
        $url = $this->client->getBaseUrl().'/platform/'.$this->client->getApiVersion().'/content-campaigns';

        try {
            $response = $this->client->getHttpClient()->post($url, [
                'headers' => [
                    'Authorization' => 'Bearer '.$this->client->getAccessToken(),
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
