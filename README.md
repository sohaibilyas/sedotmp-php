# SedoTMP PHP SDK

A simple PHP SDK for interacting with SedoTMP - Sedo Traffic Monetization Platform (sedotmp.com).

> **Requires [PHP 8.2+](https://php.net/releases/)**

## Installation

Install via [Composer](https://getcomposer.org):

```bash
composer require sohaibilyas/sedotmp-php
```

## Usage

### Getting Started

```php
use SohaibIlyas\SedoTmp\SedoTmp;

$client = new SedoTmp('your-client-id', 'your-client-secret');
```

### Authentication

#### Getting a New Access Token

Call `getAccessToken()` to fetch and cache an access token:

```php
$client = new SedoTmp('your-client-id', 'your-client-secret');

$token = $client->getAccessToken();

$categories = $client->content()->getCategories();
$campaigns = $client->platform()->getContentCampaigns(0);
```

#### Using an Existing Access Token

If you already have a token (e.g., from cache or session), use `setAccessToken()` to avoid making an authentication request:

```php
$client = new SedoTmp('your-client-id', 'your-client-secret');
$client->setAccessToken('your-existing-token');

$categories = $client->content()->getCategories();
```

#### Complete Example with Token Caching

```php
$client = new SedoTmp('your-client-id', 'your-client-secret');

if (isset($_SESSION['sedo_token'])) {
    $client->setAccessToken($_SESSION['sedo_token']);
} else {
    $token = $client->getAccessToken();
    $_SESSION['sedo_token'] = $token;
}

$categories = $client->content()->getCategories();
$campaigns = $client->platform()->getContentCampaigns(0);
```

### Content API

#### Get Categories

```php
$categories = $client->content()->getCategories();
```

### Platform API

#### Get Content Campaigns

Retrieve content campaigns with pagination:

```php
$campaigns = $client->platform()->getContentCampaigns(page: 0);
```

#### Create Content Campaign

Create a new content campaign:

```php
$campaignData = [
    'publishDomainName' => 'sohaibilyas.com',
    'article' => [
        'country' => 'US',
        'locale' => 'en',
        'featuredImage' => [
            'generate' => true
        ],
        'title' => 'Summer vacation',
        'excerpt' => 'The best summer vacation deals',
        'topics' => ['Summer vacation'],
        'categoryId' => '2e5c8fbb-f078-498b-82e5-d45263e21f67',
        'type' => 'CreateArticle',
    ],
    'campaign' => [
        'name' => 'Summer vacation',
        'trackingData' => [
            'trafficSource' => 'META',
            'trackingSettings' => [
                'type' => 'PixelMetaTrackingSettings',
                'pixelMetaPixelId' => '012345678910',
                'pixelMetaLandingPageEvent' => 'Subscribe',
                'pixelMetaClickEvent' => 'Purchase'
            ],
            'trackingMethod' => 'PIXEL'
        ],
        'type' => 'CreateCampaign'
    ],
];

$result = $client->platform()->createContentCampaign($campaignData);
```

### Custom Configuration

You can optionally provide custom API version, base URL, and auth URL:

```php
$client = new SedoTmp(
    'your-client-id',
    'your-client-secret',
    'v1',
    'https://custom-api.sedotmp.com',
    'https://custom-auth.sedotmp.com/oauth/token'
);
```

## Development

### Testing

All tests use mocked HTTP clients - no real API calls are made during testing.

Run unit tests using **PEST**:
```bash
composer test:unit
```

Run the entire test suite:
```bash
composer test
```

### Code Quality

Keep a modern codebase with **Pint**:
```bash
composer lint
```

Run refactors using **Rector**:
```bash
composer refactor
```

Run static analysis using **PHPStan**:
```bash
composer test:types
```

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
