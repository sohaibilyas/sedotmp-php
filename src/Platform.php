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
    private function parseResponseBody(string $rawBody, string $contentType): array
    {
        if (str_contains($contentType, 'application/x-ndjson') || str_contains($contentType, 'ndjson')) {
            $data = [];
            $lines = explode("\n", $rawBody);
            foreach ($lines as $line) {
                $line = trim($line);
                if ($line === '') {
                    continue;
                }
                if ($line === '0') {
                    continue;
                }
                $decoded = json_decode($line, true);
                if (is_array($decoded)) {
                    $data[] = $decoded;
                }
            }

            return $data;
        }

        $data = json_decode($rawBody, true);
        if (! is_array($data)) {
            throw new \RuntimeException('Invalid response format from API');
        }

        return $data;
    }

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

            return $this->parseResponseBody(
                $response->getBody()->getContents(),
                $response->getHeaderLine('Content-Type')
            );
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

            return $this->parseResponseBody(
                $response->getBody()->getContents(),
                $response->getHeaderLine('Content-Type')
            );
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

            return $this->parseResponseBody(
                $response->getBody()->getContents(),
                $response->getHeaderLine('Content-Type')
            );
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

            return $this->parseResponseBody(
                $response->getBody()->getContents(),
                $response->getHeaderLine('Content-Type')
            );
        } catch (GuzzleException $e) {
            throw new \RuntimeException('Failed to fetch campaign report from SedoTMP API: '.$e->getMessage(), 0, $e);
        }
    }

    /**
     * @param  array<string>  $dimensions
     * @param  array<string, mixed>|null  $filter
     * @param  array<string, mixed>|null  $pagination
     * @return array<int|string, mixed>
     */
    public function getKeywordPerformanceReport(
        array $dimensions = [],
        ?array $filter = null,
        ?string $sort = null,
        ?array $pagination = null
    ): array {
        $url = $this->client->getBaseUrl().'/platform/'.$this->client->getApiVersion().'/keyword-performance-report';
        $queryParams = [];

        foreach ($dimensions as $dimension) {
            $queryParams[] = 'dimensions='.urlencode($dimension);
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

            return $this->parseResponseBody(
                $response->getBody()->getContents(),
                $response->getHeaderLine('Content-Type')
            );
        } catch (GuzzleException $e) {
            throw new \RuntimeException('Failed to fetch keyword performance report from SedoTMP API: '.$e->getMessage(), 0, $e);
        }
    }

    /**
     * @param  array<string, mixed>|null  $filter
     * @param  array<string, mixed>|null  $page
     * @return array<int|string, mixed>
     */
    public function getPostbackTemplates(?array $filter = null, ?array $page = null): array
    {
        $url = $this->client->getBaseUrl().'/platform/'.$this->client->getApiVersion().'/tracking-data-templates/postback';
        $queryParams = [];

        if ($filter !== null && count($filter) > 0) {
            $queryParams[] = 'filter='.urlencode(json_encode($filter, JSON_THROW_ON_ERROR));
        }

        if ($page !== null) {
            if (isset($page['page'])) {
                $queryParams[] = 'page='.$page['page'];
            }
            if (isset($page['size'])) {
                $queryParams[] = 'size='.$page['size'];
            }
            if (isset($page['sort'])) {
                $queryParams[] = 'sort='.urlencode((string) $page['sort']);
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

            return $this->parseResponseBody(
                $response->getBody()->getContents(),
                $response->getHeaderLine('Content-Type')
            );
        } catch (GuzzleException $e) {
            throw new \RuntimeException('Failed to fetch postback templates from SedoTMP API: '.$e->getMessage(), 0, $e);
        }
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<int|string, mixed>
     */
    public function createPostbackTemplate(array $data): array
    {
        $url = $this->client->getBaseUrl().'/platform/'.$this->client->getApiVersion().'/tracking-data-templates/postback';

        try {
            $response = $this->client->getHttpClient()->post($url, [
                'headers' => [
                    'Authorization' => 'Bearer '.$this->client->getAccessToken(),
                    'Content-Type' => 'application/json',
                ],
                'json' => $data,
            ]);

            return $this->parseResponseBody(
                $response->getBody()->getContents(),
                $response->getHeaderLine('Content-Type')
            );
        } catch (GuzzleException $e) {
            throw new \RuntimeException('Failed to create postback template in SedoTMP API: '.$e->getMessage(), 0, $e);
        }
    }

    /**
     * @return array<int|string, mixed>
     */
    public function getPostbackTemplate(string $id): array
    {
        $url = $this->client->getBaseUrl().'/platform/'.$this->client->getApiVersion().'/tracking-data-templates/postback/'.$id;

        try {
            $response = $this->client->getHttpClient()->get($url, [
                'headers' => [
                    'Authorization' => 'Bearer '.$this->client->getAccessToken(),
                    'Content-Type' => 'application/json',
                ],
            ]);

            return $this->parseResponseBody(
                $response->getBody()->getContents(),
                $response->getHeaderLine('Content-Type')
            );
        } catch (GuzzleException $e) {
            throw new \RuntimeException('Failed to fetch postback template from SedoTMP API: '.$e->getMessage(), 0, $e);
        }
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<int|string, mixed>
     */
    public function updatePostbackTemplate(string $id, array $data): array
    {
        $url = $this->client->getBaseUrl().'/platform/'.$this->client->getApiVersion().'/tracking-data-templates/postback/'.$id;

        try {
            $response = $this->client->getHttpClient()->put($url, [
                'headers' => [
                    'Authorization' => 'Bearer '.$this->client->getAccessToken(),
                    'Content-Type' => 'application/json',
                ],
                'json' => $data,
            ]);

            return $this->parseResponseBody(
                $response->getBody()->getContents(),
                $response->getHeaderLine('Content-Type')
            );
        } catch (GuzzleException $e) {
            throw new \RuntimeException('Failed to update postback template in SedoTMP API: '.$e->getMessage(), 0, $e);
        }
    }

    public function deletePostbackTemplate(string $id): void
    {
        $url = $this->client->getBaseUrl().'/platform/'.$this->client->getApiVersion().'/tracking-data-templates/postback/'.$id;

        try {
            $this->client->getHttpClient()->delete($url, [
                'headers' => [
                    'Authorization' => 'Bearer '.$this->client->getAccessToken(),
                    'Content-Type' => 'application/json',
                ],
            ]);
        } catch (GuzzleException $e) {
            throw new \RuntimeException('Failed to delete postback template from SedoTMP API: '.$e->getMessage(), 0, $e);
        }
    }
}
