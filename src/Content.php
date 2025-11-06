<?php

declare(strict_types=1);

namespace SohaibIlyas\SedoTmp;

use GuzzleHttp\Exception\GuzzleException;

final readonly class Content
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
    public function getCategories(): array
    {
        $url = $this->client->getBaseUrl().'/content/'.$this->client->getApiVersion().'/categories';

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
            throw new \RuntimeException('Failed to fetch categories from SedoTMP API: '.$e->getMessage(), 0, $e);
        }
    }
}
