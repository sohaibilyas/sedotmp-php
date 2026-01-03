<?php

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use SohaibIlyas\SedoTmp\SedoTmp;

it('can create a client with credentials', function (): void {
    $mockHandler = new MockHandler;
    $handlerStack = HandlerStack::create($mockHandler);
    $mockClient = new Client(['handler' => $handlerStack]);

    $client = new SedoTmp('test-client-id', 'test-client-secret', httpClient: $mockClient);

    expect($client->getClientId())->toBe('test-client-id')
        ->and($client->getClientSecret())->toBe('test-client-secret');
});

it('uses default base url', function (): void {
    $mockHandler = new MockHandler;
    $handlerStack = HandlerStack::create($mockHandler);
    $mockClient = new Client(['handler' => $handlerStack]);

    $client = new SedoTmp('test-client-id', 'test-client-secret', httpClient: $mockClient);

    expect($client->getBaseUrl())->toBe('https://api.sedotmp.com');
});

it('uses default auth url', function (): void {
    $mockHandler = new MockHandler;
    $handlerStack = HandlerStack::create($mockHandler);
    $mockClient = new Client(['handler' => $handlerStack]);

    $client = new SedoTmp('test-client-id', 'test-client-secret', httpClient: $mockClient);

    expect($client->getAuthUrl())->toBe('https://auth.sedotmp.com/oauth/token');
});

it('can use custom base url', function (): void {
    $mockHandler = new MockHandler;
    $handlerStack = HandlerStack::create($mockHandler);
    $mockClient = new Client(['handler' => $handlerStack]);

    $client = new SedoTmp('test-client-id', 'test-client-secret', 'v1', 'https://custom.api.com', httpClient: $mockClient);

    expect($client->getBaseUrl())->toBe('https://custom.api.com');
});

it('can use custom auth url', function (): void {
    $mockHandler = new MockHandler;
    $handlerStack = HandlerStack::create($mockHandler);
    $mockClient = new Client(['handler' => $handlerStack]);

    $client = new SedoTmp('test-client-id', 'test-client-secret', 'v1', 'https://api.sedotmp.com', 'https://custom.auth.com/token', $mockClient);

    expect($client->getAuthUrl())->toBe('https://custom.auth.com/token');
});

it('automatically authenticates when getting access token', function (): void {
    $mockHandler = new MockHandler([
        new Response(200, [], json_encode(['access_token' => 'test-token-123'])),
    ]);
    $handlerStack = HandlerStack::create($mockHandler);
    $mockClient = new Client(['handler' => $handlerStack]);

    $client = new SedoTmp('test-client-id', 'test-client-secret', httpClient: $mockClient);

    expect($client->getAccessToken())->toBe('test-token-123');
});

it('can set access token manually', function (): void {
    $mockHandler = new MockHandler;
    $handlerStack = HandlerStack::create($mockHandler);
    $mockClient = new Client(['handler' => $handlerStack]);

    $client = new SedoTmp('test-client-id', 'test-client-secret', httpClient: $mockClient);
    $result = $client->setAccessToken('custom-token-789');

    expect($result)->toBe($client)
        ->and($client->getAccessToken())->toBe('custom-token-789');
});

it('reuses access token across multiple requests', function (): void {
    $mockHandler = new MockHandler([
        new Response(200, [], json_encode(['access_token' => 'reusable-token'])),
        new Response(200, [], json_encode([['id' => '1', 'name' => 'Category 1']])),
        new Response(200, [], json_encode(['campaigns' => [['id' => '1']]])),
    ]);
    $handlerStack = HandlerStack::create($mockHandler);
    $mockClient = new Client(['handler' => $handlerStack]);

    $client = new SedoTmp('test-client-id', 'test-client-secret', httpClient: $mockClient);

    $client->content()->getCategories();
    $client->platform()->getContentCampaigns(0);

    expect($mockHandler->count())->toBe(0);
});

it('throws exception when authentication fails', function (): void {
    $mockHandler = new MockHandler([
        new Response(401, [], json_encode(['error' => 'unauthorized'])),
    ]);
    $handlerStack = HandlerStack::create($mockHandler);
    $mockClient = new Client(['handler' => $handlerStack]);

    $client = new SedoTmp('test-client-id', 'test-client-secret', httpClient: $mockClient);

    expect($client->getAccessToken(...))->toThrow(\RuntimeException::class);
});

it('can access content resource', function (): void {
    $mockHandler = new MockHandler;
    $handlerStack = HandlerStack::create($mockHandler);
    $mockClient = new Client(['handler' => $handlerStack]);

    $client = new SedoTmp('test-client-id', 'test-client-secret', httpClient: $mockClient);

    expect($client->content())->toBeInstanceOf(\SohaibIlyas\SedoTmp\Content::class);
});

it('can access platform resource', function (): void {
    $mockHandler = new MockHandler;
    $handlerStack = HandlerStack::create($mockHandler);
    $mockClient = new Client(['handler' => $handlerStack]);

    $client = new SedoTmp('test-client-id', 'test-client-secret', httpClient: $mockClient);

    expect($client->platform())->toBeInstanceOf(\SohaibIlyas\SedoTmp\Platform::class);
});

it('fetches categories from content api', function (): void {
    $mockHandler = new MockHandler([
        new Response(200, [], json_encode(['access_token' => 'test-token-123'])),
        new Response(200, [], json_encode([
            ['id' => '1', 'name' => 'Category 1'],
            ['id' => '2', 'name' => 'Category 2'],
        ])),
    ]);
    $handlerStack = HandlerStack::create($mockHandler);
    $mockClient = new Client(['handler' => $handlerStack]);

    $client = new SedoTmp('test-client-id', 'test-client-secret', httpClient: $mockClient);
    $categories = $client->content()->getCategories();

    expect($categories)->toBeArray()
        ->and($categories)->toHaveCount(2)
        ->and($categories[0]['name'])->toBe('Category 1');
});

it('fetches content campaigns from platform api', function (): void {
    $mockHandler = new MockHandler([
        new Response(200, [], json_encode(['access_token' => 'test-token-123'])),
        new Response(200, [], json_encode([
            'campaigns' => [
                ['id' => '1', 'name' => 'Campaign 1'],
            ],
        ])),
    ]);
    $handlerStack = HandlerStack::create($mockHandler);
    $mockClient = new Client(['handler' => $handlerStack]);

    $client = new SedoTmp('test-client-id', 'test-client-secret', httpClient: $mockClient);
    $campaigns = $client->platform()->getContentCampaigns(0);

    expect($campaigns)->toBeArray()
        ->and($campaigns)->toHaveKey('campaigns');
});

it('fetches single content campaign from platform api', function (): void {
    $mockHandler = new MockHandler([
        new Response(200, [], json_encode(['access_token' => 'test-token-123'])),
        new Response(200, [], json_encode([
            'id' => '310a2938-6824-4bf9-afdf-994c3a673864',
            'name' => 'Campaign 1',
            'status' => 'active',
        ])),
    ]);
    $handlerStack = HandlerStack::create($mockHandler);
    $mockClient = new Client(['handler' => $handlerStack]);

    $client = new SedoTmp('test-client-id', 'test-client-secret', httpClient: $mockClient);
    $campaign = $client->platform()->getContentCampaign('310a2938-6824-4bf9-afdf-994c3a673864');

    expect($campaign)->toBeArray()
        ->and($campaign['id'])->toBe('310a2938-6824-4bf9-afdf-994c3a673864')
        ->and($campaign['name'])->toBe('Campaign 1');
});

it('creates content campaign via platform api', function (): void {
    $mockHandler = new MockHandler([
        new Response(200, [], json_encode(['access_token' => 'test-token-123'])),
        new Response(201, [], json_encode(['id' => 'campaign-123', 'status' => 'created'])),
    ]);
    $handlerStack = HandlerStack::create($mockHandler);
    $mockClient = new Client(['handler' => $handlerStack]);

    $client = new SedoTmp('test-client-id', 'test-client-secret', httpClient: $mockClient);
    $result = $client->platform()->createContentCampaign([
        'name' => 'Test Campaign',
    ]);

    expect($result)->toBeArray()
        ->and($result['id'])->toBe('campaign-123')
        ->and($result['status'])->toBe('created');
});

it('fetches campaign report from platform api with ndjson response', function (): void {
    $ndjsonResponse = json_encode(['date' => '2024-01-01', 'clicks' => 100])."\n".
                      json_encode(['date' => '2024-01-02', 'clicks' => 200])."\n".
                      json_encode(['date' => '2024-01-03', 'clicks' => 150]);

    $mockHandler = new MockHandler([
        new Response(200, [], json_encode(['access_token' => 'test-token-123'])),
        new Response(200, ['Content-Type' => 'application/x-ndjson'], $ndjsonResponse),
    ]);
    $handlerStack = HandlerStack::create($mockHandler);
    $mockClient = new Client(['handler' => $handlerStack]);

    $client = new SedoTmp('test-client-id', 'test-client-secret', httpClient: $mockClient);
    $report = $client->platform()->getCampaignReport(
        dimensions: ['DATE', 'COUNTRY'],
        filter: ['startDate' => ['year' => 2024, 'month' => 1, 'day' => 1]],
        sort: 'CLICKS,asc',
        pagination: ['page' => 0, 'size' => 10]
    );

    expect($report)->toBeArray()
        ->and($report)->toHaveCount(3)
        ->and($report[0]['date'])->toBe('2024-01-01')
        ->and($report[0]['clicks'])->toBe(100)
        ->and($report[1]['date'])->toBe('2024-01-02')
        ->and($report[1]['clicks'])->toBe(200)
        ->and($report[2]['date'])->toBe('2024-01-03')
        ->and($report[2]['clicks'])->toBe(150);
});

it('fetches campaign report with offset-based pagination', function (): void {
    $ndjsonResponse = json_encode(['date' => '2024-01-01', 'revenue' => 50.5]);

    $mockHandler = new MockHandler([
        new Response(200, [], json_encode(['access_token' => 'test-token-123'])),
        new Response(200, ['Content-Type' => 'application/x-ndjson'], $ndjsonResponse),
    ]);
    $handlerStack = HandlerStack::create($mockHandler);
    $mockClient = new Client(['handler' => $handlerStack]);

    $client = new SedoTmp('test-client-id', 'test-client-secret', httpClient: $mockClient);
    $report = $client->platform()->getCampaignReport(
        dimensions: ['DATE'],
        pagination: ['offset' => 0, 'limit' => 100]
    );

    expect($report)->toBeArray()
        ->and($report)->toHaveCount(1)
        ->and($report[0]['date'])->toBe('2024-01-01');
});

it('fetches keyword performance report from platform api with ndjson response', function (): void {
    $ndjsonResponse = json_encode([
        'partner' => 'partner2',
        'date' => '2024-01-01',
        'campaignId' => '1',
        'campaignName' => 'summer vacation',
        'country' => 'US',
        'deviceType' => 'Desktop',
        'keywords' => 'summer+vacation',
        'clicks' => 100,
        'searches' => 200,
        'coverage' => 1,
        'coveragePercent' => 0.5,
        'estimatedRevenue' => 1000,
        'ctr' => 0.1,
        'estimatedCpc' => 0.5,
    ])."\n".json_encode([
        'partner' => 'partner1',
        'date' => '2024-01-02',
        'campaignId' => '2',
        'campaignName' => 'winter sale',
        'country' => 'DE',
        'deviceType' => 'Mobile',
        'keywords' => 'winter+sale',
        'clicks' => 50,
        'searches' => 100,
        'coverage' => 1,
        'coveragePercent' => 0.5,
        'estimatedRevenue' => 500,
        'ctr' => 0.2,
        'estimatedCpc' => 0.4,
    ]);

    $mockHandler = new MockHandler([
        new Response(200, [], json_encode(['access_token' => 'test-token-123'])),
        new Response(200, ['Content-Type' => 'application/x-ndjson'], $ndjsonResponse),
    ]);
    $handlerStack = HandlerStack::create($mockHandler);
    $mockClient = new Client(['handler' => $handlerStack]);

    $client = new SedoTmp('test-client-id', 'test-client-secret', httpClient: $mockClient);
    $report = $client->platform()->getKeywordPerformanceReport(
        dimensions: ['DATE', 'COUNTRY', 'DEVICE_TYPE'],
        filter: ['startDate' => ['year' => 2024, 'month' => 1, 'day' => 1]],
        sort: 'CLICKS,asc',
        pagination: ['page' => 0, 'size' => 10]
    );

    expect($report)->toBeArray()
        ->and($report)->toHaveCount(2)
        ->and($report[0]['keywords'])->toBe('summer+vacation')
        ->and($report[0]['clicks'])->toBe(100)
        ->and($report[0]['estimatedRevenue'])->toBe(1000)
        ->and($report[1]['keywords'])->toBe('winter+sale')
        ->and($report[1]['clicks'])->toBe(50);
});

it('fetches keyword performance report with offset-based pagination', function (): void {
    $ndjsonResponse = json_encode([
        'date' => '2024-01-01',
        'keywords' => 'test+keyword',
        'clicks' => 75,
        'estimatedRevenue' => 250.5,
    ]);

    $mockHandler = new MockHandler([
        new Response(200, [], json_encode(['access_token' => 'test-token-123'])),
        new Response(200, ['Content-Type' => 'application/x-ndjson'], $ndjsonResponse),
    ]);
    $handlerStack = HandlerStack::create($mockHandler);
    $mockClient = new Client(['handler' => $handlerStack]);

    $client = new SedoTmp('test-client-id', 'test-client-secret', httpClient: $mockClient);
    $report = $client->platform()->getKeywordPerformanceReport(
        dimensions: ['DATE'],
        pagination: ['offset' => 0, 'limit' => 100]
    );

    expect($report)->toBeArray()
        ->and($report)->toHaveCount(1)
        ->and($report[0]['keywords'])->toBe('test+keyword')
        ->and($report[0]['clicks'])->toBe(75);
});

it('fetches postback templates from platform api', function (): void {
    $mockHandler = new MockHandler([
        new Response(200, [], json_encode(['access_token' => 'test-token-123'])),
        new Response(200, [], json_encode([
            [
                'id' => 'cf1a429f-e596-4648-83a2-5a3045b2276a',
                'name' => 'Template-123',
                'postbacks' => [
                    [
                        'eventName' => 'CLICK',
                        'url' => 'https://your-tracking-url.com/cf/cv?click_id={click_id}&payout={epayout}',
                        'clickIdParam' => 'click_id',
                    ],
                ],
                'partner' => 'partner2',
                'createdDate' => '2024-01-01T18:00:00Z',
                'createdBy' => 'John Doe',
            ],
        ])),
    ]);
    $handlerStack = HandlerStack::create($mockHandler);
    $mockClient = new Client(['handler' => $handlerStack]);

    $client = new SedoTmp('test-client-id', 'test-client-secret', httpClient: $mockClient);
    $templates = $client->platform()->getPostbackTemplates();

    expect($templates)->toBeArray()
        ->and($templates)->toHaveCount(1)
        ->and($templates[0]['id'])->toBe('cf1a429f-e596-4648-83a2-5a3045b2276a')
        ->and($templates[0]['name'])->toBe('Template-123');
});

it('fetches postback templates with filter and pagination', function (): void {
    $mockHandler = new MockHandler([
        new Response(200, [], json_encode(['access_token' => 'test-token-123'])),
        new Response(200, [], json_encode([
            [
                'id' => 'cf1a429f-e596-4648-83a2-5a3045b2276a',
                'name' => 'Template-123',
                'postbacks' => [],
            ],
        ])),
    ]);
    $handlerStack = HandlerStack::create($mockHandler);
    $mockClient = new Client(['handler' => $handlerStack]);

    $client = new SedoTmp('test-client-id', 'test-client-secret', httpClient: $mockClient);
    $templates = $client->platform()->getPostbackTemplates(
        filter: ['name' => 'Template-123'],
        page: ['page' => 0, 'size' => 10, 'sort' => 'name,asc']
    );

    expect($templates)->toBeArray()
        ->and($templates)->toHaveCount(1);
});

it('creates postback template via platform api', function (): void {
    $mockHandler = new MockHandler([
        new Response(200, [], json_encode(['access_token' => 'test-token-123'])),
        new Response(200, [], json_encode([
            'id' => 'cf1a429f-e596-4648-83a2-5a3045b2276a',
            'name' => 'Template-123',
            'postbacks' => [
                [
                    'eventName' => 'CLICK',
                    'url' => 'https://your-tracking-url.com/cf/cv?click_id={click_id}&payout={epayout}',
                    'clickIdParam' => 'click_id',
                ],
            ],
            'partner' => 'partner2',
            'createdDate' => '2024-01-01T18:00:00Z',
            'createdBy' => 'John Doe',
        ])),
    ]);
    $handlerStack = HandlerStack::create($mockHandler);
    $mockClient = new Client(['handler' => $handlerStack]);

    $client = new SedoTmp('test-client-id', 'test-client-secret', httpClient: $mockClient);
    $result = $client->platform()->createPostbackTemplate([
        'name' => 'Template-123',
        'postbacks' => [
            [
                'eventName' => 'CLICK',
                'url' => 'https://your-tracking-url.com/cf/cv?click_id={click_id}&payout={epayout}',
                'clickIdParam' => 'click_id',
            ],
        ],
    ]);

    expect($result)->toBeArray()
        ->and($result['id'])->toBe('cf1a429f-e596-4648-83a2-5a3045b2276a')
        ->and($result['name'])->toBe('Template-123')
        ->and($result['postbacks'])->toHaveCount(1);
});

it('fetches single postback template from platform api', function (): void {
    $mockHandler = new MockHandler([
        new Response(200, [], json_encode(['access_token' => 'test-token-123'])),
        new Response(200, [], json_encode([
            'id' => 'cf1a429f-e596-4648-83a2-5a3045b2276a',
            'name' => 'Template-123',
            'postbacks' => [
                [
                    'eventName' => 'CLICK',
                    'url' => 'https://your-tracking-url.com/cf/cv?click_id={click_id}&payout={epayout}',
                    'clickIdParam' => 'click_id',
                ],
            ],
            'partner' => 'partner2',
        ])),
    ]);
    $handlerStack = HandlerStack::create($mockHandler);
    $mockClient = new Client(['handler' => $handlerStack]);

    $client = new SedoTmp('test-client-id', 'test-client-secret', httpClient: $mockClient);
    $template = $client->platform()->getPostbackTemplate('cf1a429f-e596-4648-83a2-5a3045b2276a');

    expect($template)->toBeArray()
        ->and($template['id'])->toBe('cf1a429f-e596-4648-83a2-5a3045b2276a')
        ->and($template['name'])->toBe('Template-123');
});

it('updates postback template via platform api', function (): void {
    $mockHandler = new MockHandler([
        new Response(200, [], json_encode(['access_token' => 'test-token-123'])),
        new Response(200, [], json_encode([
            'id' => 'cf1a429f-e596-4648-83a2-5a3045b2276a',
            'name' => 'Updated-Template',
            'postbacks' => [
                [
                    'eventName' => 'SEARCH',
                    'url' => 'https://your-tracking-url.com/cf/cv?click_id={click_id}',
                    'clickIdParam' => 'click_id',
                ],
            ],
            'partner' => 'partner2',
        ])),
    ]);
    $handlerStack = HandlerStack::create($mockHandler);
    $mockClient = new Client(['handler' => $handlerStack]);

    $client = new SedoTmp('test-client-id', 'test-client-secret', httpClient: $mockClient);
    $result = $client->platform()->updatePostbackTemplate('cf1a429f-e596-4648-83a2-5a3045b2276a', [
        'name' => 'Updated-Template',
        'postbacks' => [
            [
                'eventName' => 'SEARCH',
                'url' => 'https://your-tracking-url.com/cf/cv?click_id={click_id}',
                'clickIdParam' => 'click_id',
            ],
        ],
    ]);

    expect($result)->toBeArray()
        ->and($result['id'])->toBe('cf1a429f-e596-4648-83a2-5a3045b2276a')
        ->and($result['name'])->toBe('Updated-Template');
});

it('deletes postback template via platform api', function (): void {
    $mockHandler = new MockHandler([
        new Response(200, [], json_encode(['access_token' => 'test-token-123'])),
        new Response(204, [], ''),
    ]);
    $handlerStack = HandlerStack::create($mockHandler);
    $mockClient = new Client(['handler' => $handlerStack]);

    $client = new SedoTmp('test-client-id', 'test-client-secret', httpClient: $mockClient);
    $client->platform()->deletePostbackTemplate('cf1a429f-e596-4648-83a2-5a3045b2276a');

    expect($mockHandler->count())->toBe(0);
});

it('fetches traffic source templates from platform api', function (): void {
    $mockHandler = new MockHandler([
        new Response(200, [], json_encode(['access_token' => 'test-token-123'])),
        new Response(200, [], json_encode([
            [
                'id' => 'cf1a429f-e596-4648-83a2-5a3045b2276a',
                'name' => 'Meta-Template',
                'trafficSource' => 'META',
                'trackingMethod' => 'PIXEL',
                'trackingSettings' => [],
                'partner' => 'partner2',
            ],
        ])),
    ]);
    $handlerStack = HandlerStack::create($mockHandler);
    $mockClient = new Client(['handler' => $handlerStack]);

    $client = new SedoTmp('test-client-id', 'test-client-secret', httpClient: $mockClient);
    $templates = $client->platform()->getTrafficSourceTemplates();

    expect($templates)->toBeArray()
        ->and($templates)->toHaveCount(1)
        ->and($templates[0]['trafficSource'])->toBe('META');
});

it('creates traffic source template via platform api', function (): void {
    $mockHandler = new MockHandler([
        new Response(200, [], json_encode(['access_token' => 'test-token-123'])),
        new Response(200, [], json_encode([
            'id' => 'cf1a429f-e596-4648-83a2-5a3045b2276a',
            'name' => 'Meta-Template',
            'trafficSource' => 'META',
            'trackingMethod' => 'PIXEL',
            'partner' => 'partner2',
        ])),
    ]);
    $handlerStack = HandlerStack::create($mockHandler);
    $mockClient = new Client(['handler' => $handlerStack]);

    $client = new SedoTmp('test-client-id', 'test-client-secret', httpClient: $mockClient);
    $result = $client->platform()->createTrafficSourceTemplate([
        'name' => 'Meta-Template',
        'trafficSource' => 'META',
        'trackingMethod' => 'PIXEL',
    ]);

    expect($result)->toBeArray()
        ->and($result['id'])->toBe('cf1a429f-e596-4648-83a2-5a3045b2276a')
        ->and($result['trafficSource'])->toBe('META');
});

it('fetches single traffic source template from platform api', function (): void {
    $mockHandler = new MockHandler([
        new Response(200, [], json_encode(['access_token' => 'test-token-123'])),
        new Response(200, [], json_encode([
            'id' => 'cf1a429f-e596-4648-83a2-5a3045b2276a',
            'name' => 'Meta-Template',
            'trafficSource' => 'META',
            'trackingMethod' => 'PIXEL',
        ])),
    ]);
    $handlerStack = HandlerStack::create($mockHandler);
    $mockClient = new Client(['handler' => $handlerStack]);

    $client = new SedoTmp('test-client-id', 'test-client-secret', httpClient: $mockClient);
    $template = $client->platform()->getTrafficSourceTemplate('cf1a429f-e596-4648-83a2-5a3045b2276a');

    expect($template)->toBeArray()
        ->and($template['id'])->toBe('cf1a429f-e596-4648-83a2-5a3045b2276a');
});

it('updates traffic source template via platform api', function (): void {
    $mockHandler = new MockHandler([
        new Response(200, [], json_encode(['access_token' => 'test-token-123'])),
        new Response(200, [], json_encode([
            'id' => 'cf1a429f-e596-4648-83a2-5a3045b2276a',
            'name' => 'Updated-Meta-Template',
            'trafficSource' => 'META',
            'trackingMethod' => 'S2S',
        ])),
    ]);
    $handlerStack = HandlerStack::create($mockHandler);
    $mockClient = new Client(['handler' => $handlerStack]);

    $client = new SedoTmp('test-client-id', 'test-client-secret', httpClient: $mockClient);
    $result = $client->platform()->updateTrafficSourceTemplate('cf1a429f-e596-4648-83a2-5a3045b2276a', [
        'name' => 'Updated-Meta-Template',
        'trafficSource' => 'META',
        'trackingMethod' => 'S2S',
    ]);

    expect($result)->toBeArray()
        ->and($result['name'])->toBe('Updated-Meta-Template');
});

it('deletes traffic source template via platform api', function (): void {
    $mockHandler = new MockHandler([
        new Response(200, [], json_encode(['access_token' => 'test-token-123'])),
        new Response(204, [], ''),
    ]);
    $handlerStack = HandlerStack::create($mockHandler);
    $mockClient = new Client(['handler' => $handlerStack]);

    $client = new SedoTmp('test-client-id', 'test-client-secret', httpClient: $mockClient);
    $client->platform()->deleteTrafficSourceTemplate('cf1a429f-e596-4648-83a2-5a3045b2276a');

    expect($mockHandler->count())->toBe(0);
});
