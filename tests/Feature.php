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
