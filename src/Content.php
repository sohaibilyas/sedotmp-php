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

            $data = json_decode($response->getBody()->getContents(), true);

            if (! is_array($data)) {
                throw new \RuntimeException('Invalid response format from API');
            }

            return $data;
        } catch (GuzzleException $e) {
            throw new \RuntimeException('Failed to fetch categories from SedoTMP API: '.$e->getMessage(), 0, $e);
        }
    }
}
