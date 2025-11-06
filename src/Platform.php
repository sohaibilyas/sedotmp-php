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

    /**
     * @param  array<string>|null  $dimensions
     * @param  array<string, mixed>|null  $filter
     * @param  array<string, mixed>|null  $pagination
     * @return array<int|string, mixed>
     */
    public function getCampaignReport(
        ?array $dimensions = null,
        ?array $filter = null,
        ?string $sort = null,
        ?array $pagination = null
    ): array {
        $url = $this->client->getBaseUrl().'/platform/'.$this->client->getApiVersion().'/campaign-report';
        $queryParams = [];

        if ($dimensions !== null && count($dimensions) > 0) {
            foreach ($dimensions as $dimension) {
                $queryParams[] = 'dimensions='.urlencode($dimension);
            }
        }

        if ($filter !== null && count($filter) > 0) {
            $queryParams[] = 'filter='.urlencode(json_encode($filter, JSON_THROW_ON_ERROR));
        }

        if ($sort !== null) {
            $queryParams[] = 'sort='.urlencode($sort);
        }

        if ($pagination !== null) {
            if (isset($pagination['offset']) && isset($pagination['limit'])) {
                $queryParams[] = 'offset='.$pagination['offset'];
                $queryParams[] = 'limit='.$pagination['limit'];
            } elseif (isset($pagination['page']) && isset($pagination['size'])) {
                $queryParams[] = 'page='.$pagination['page'];
                $queryParams[] = 'size='.$pagination['size'];
            }
        }

        if (count($queryParams) > 0) {
            $url .= '?'.implode('&', $queryParams);
        }

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
            throw new \RuntimeException('Failed to fetch campaign report from SedoTMP API: '.$e->getMessage(), 0, $e);
        }
    }
}
