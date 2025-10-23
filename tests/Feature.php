<?php

use SohaibIlyas\SedoTmp\SedoTmp;

it('can create a client with credentials', function (): void {
    $client = new SedoTmp('test-client-id', 'test-client-secret');

    expect($client->getClientId())->toBe('test-client-id')
        ->and($client->getClientSecret())->toBe('test-client-secret');
});

it('uses default base url', function (): void {
    $client = new SedoTmp('test-client-id', 'test-client-secret');

    expect($client->getBaseUrl())->toBe('https://api.sedotmp.com');
});

it('uses default auth url', function (): void {
    $client = new SedoTmp('test-client-id', 'test-client-secret');

    expect($client->getAuthUrl())->toBe('https://auth.sedotmp.com/oauth/token');
});

it('can use custom base url', function (): void {
    $client = new SedoTmp('test-client-id', 'test-client-secret', 'https://custom.api.com');

    expect($client->getBaseUrl())->toBe('https://custom.api.com');
});

it('can use custom auth url', function (): void {
    $client = new SedoTmp('test-client-id', 'test-client-secret', 'https://api.sedotmp.com', 'https://custom.auth.com/token');

    expect($client->getAuthUrl())->toBe('https://custom.auth.com/token');
});

it('returns null access token before authentication', function (): void {
    $client = new SedoTmp('test-client-id', 'test-client-secret');

    expect($client->getAccessToken())->toBeNull();
});
