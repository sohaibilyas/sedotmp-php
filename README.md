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

#### Get Single Content Campaign

Retrieve a specific content campaign by ID:

```php
$campaign = $client->platform()->getContentCampaign('310a2938-6824-4bf9-afdf-994c3a673864');
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

#### Get Campaign Report

Retrieve campaign report data with dimensions, filters, sorting, and pagination:

```php
$report = $client->platform()->getCampaignReport(
    dimensions: ['DATE', 'HOUR', 'COUNTRY'],
    filter: [
        'startDate' => ['year' => 2024, 'month' => 1, 'day' => 1],
        'endDate' => ['year' => 2024, 'month' => 1, 'day' => 31],
        'campaignId' => '310a2938-6824-4bf9-afdf-994c3a673864',
    ],
    sort: 'CLICKS,asc',
    pagination: ['page' => 0, 'size' => 10]
);
```

**Available dimensions:**
- `DATE`
- `HOUR`
- `PARTNER`
- `CAMPAIGN_ID`
- `COUNTRY`
- `DEVICE_TYPE`

**Available sort fields:**
- `RELATED_LINKS_REQUESTS`
- `RELATED_LINKS_IMPRESSIONS`
- `RELATED_LINKS_CLICKS`
- `RELATED_LINKS_RPM`
- `AD_REQUESTS`
- `MATCHED_AD_REQUESTS`
- `AD_IMPRESSIONS`
- `IMPRESSIONS`
- `CLICKS`
- `CTR`
- `AD_CTR`
- `CPC`
- `AD_RPM`
- `CONVERSION_RATE`
- `REVENUE`
- `DATE`
- `HOUR`
- `PARTNER`
- `CAMPAIGN_ID`
- `COUNTRY`
- `DEVICE_TYPE`

**Pagination options:**

Use page-based pagination:
```php
$report = $client->platform()->getCampaignReport(
    pagination: ['page' => 0, 'size' => 10]
);
```

Or use offset-based pagination:
```php
$report = $client->platform()->getCampaignReport(
    pagination: ['offset' => 0, 'limit' => 100]
);
```

**Note:** If neither `startDate` nor `endDate` is provided in the filter, they will default to yesterday's date.

#### Get Keyword Performance Report

Retrieve keyword performance data with dimensions, filters, sorting, and pagination:

```php
$report = $client->platform()->getKeywordPerformanceReport(
    dimensions: ['DATE', 'COUNTRY', 'DEVICE_TYPE'],
    filter: [
        'startDate' => ['year' => 2024, 'month' => 1, 'day' => 1],
        'endDate' => ['year' => 2024, 'month' => 1, 'day' => 31],
    ],
    sort: 'CLICKS,desc',
    pagination: ['page' => 0, 'size' => 50]
);
```

**Available dimensions:**
- `DATE`
- `PARTNER`
- `CAMPAIGN_ID`
- `COUNTRY`
- `DEVICE_TYPE`

### Postback Templates

Postback templates define reusable S2S callback configurations for conversion events.

#### Get All Postback Templates

```php
$templates = $client->platform()->getPostbackTemplates();

$templates = $client->platform()->getPostbackTemplates(
    filter: ['name' => 'My Template'],
    page: ['page' => 0, 'size' => 10, 'sort' => 'name,asc']
);
```

#### Get Single Postback Template

```php
$template = $client->platform()->getPostbackTemplate('cf1a429f-e596-4648-83a2-5a3045b2276a');
```

#### Create Postback Template

```php
$template = $client->platform()->createPostbackTemplate([
    'name' => 'My Postback Template',
    'postbacks' => [
        [
            'eventName' => 'CLICK',
            'url' => 'https://your-tracker.com/postback?click_id={click_id}&payout={epayout}',
            'clickIdParam' => 'click_id',
        ],
        [
            'eventName' => 'SEARCH',
            'url' => 'https://your-tracker.com/postback?click_id={click_id}&event=search',
            'clickIdParam' => 'click_id',
        ],
    ],
]);
```

#### Update Postback Template

```php
$template = $client->platform()->updatePostbackTemplate('cf1a429f-e596-4648-83a2-5a3045b2276a', [
    'name' => 'Updated Template Name',
    'postbacks' => [
        [
            'eventName' => 'CLICK',
            'url' => 'https://your-tracker.com/postback?click_id={click_id}',
            'clickIdParam' => 'click_id',
        ],
    ],
]);
```

#### Delete Postback Template

```php
$client->platform()->deletePostbackTemplate('cf1a429f-e596-4648-83a2-5a3045b2276a');
```

### Traffic Source Templates

Traffic source templates define reusable tracking configurations for different traffic sources.

#### Get All Traffic Source Templates

```php
$templates = $client->platform()->getTrafficSourceTemplates();

$templates = $client->platform()->getTrafficSourceTemplates(
    filter: ['name' => 'Meta Template'],
    page: ['page' => 0, 'size' => 10]
);
```

#### Get Single Traffic Source Template

```php
$template = $client->platform()->getTrafficSourceTemplate('cf1a429f-e596-4648-83a2-5a3045b2276a');
```

#### Create Traffic Source Template

```php
$template = $client->platform()->createTrafficSourceTemplate([
    'name' => 'Meta Pixel Template',
    'trafficSource' => 'META',
    'trackingMethod' => 'PIXEL',
    'trackingSettings' => [
        'type' => 'PixelMetaTrackingSettings',
        'pixelMetaPixelId' => '012345678910',
        'pixelMetaLandingPageEvent' => 'Subscribe',
        'pixelMetaClickEvent' => 'Purchase',
    ],
]);
```

#### Update Traffic Source Template

```php
$template = $client->platform()->updateTrafficSourceTemplate('cf1a429f-e596-4648-83a2-5a3045b2276a', [
    'name' => 'Updated Meta Template',
    'trafficSource' => 'META',
    'trackingMethod' => 'S2S',
    'trackingSettings' => [
        'type' => 'S2sMetaTrackingSettings',
        's2sMetaPixelId' => '012345678910',
        's2sMetaAccessToken' => 'your-access-token',
    ],
]);
```

#### Delete Traffic Source Template

```php
$client->platform()->deleteTrafficSourceTemplate('cf1a429f-e596-4648-83a2-5a3045b2276a');
```

### Using Templates with Content Campaigns

You can reference templates when creating content campaigns to automatically apply pre-configured tracking settings:

```php
$campaignData = [
    'publishDomainName' => 'example.com',
    'article' => [
        'country' => 'US',
        'locale' => 'en',
        'featuredImage' => ['generate' => true],
        'title' => 'Summer vacation deals',
        'topics' => ['Summer vacation'],
        'categoryId' => '2e5c8fbb-f078-498b-82e5-d45263e21f67',
        'type' => 'CreateArticle',
    ],
    'campaign' => [
        'name' => 'Summer Campaign',
        'trackingData' => [
            'trafficSourceTemplateId' => 'cf1a429f-e596-4648-83a2-5a3045b2276a',
            'postbackTemplateId' => 'df2b530g-f697-5759-94b3-6b4156c3387b',
        ],
        'type' => 'CreateCampaign',
    ],
];

$result = $client->platform()->createContentCampaign($campaignData);
```

You can also reference templates by name:

```php
$campaignData = [
    'publishDomainName' => 'example.com',
    'article' => [
        'country' => 'US',
        'locale' => 'en',
        'featuredImage' => ['generate' => true],
        'title' => 'Winter sale promotions',
        'topics' => ['Winter sale'],
        'categoryId' => '2e5c8fbb-f078-498b-82e5-d45263e21f67',
        'type' => 'CreateArticle',
    ],
    'campaign' => [
        'name' => 'Winter Campaign',
        'trackingData' => [
            'trafficSourceTemplateName' => 'Meta Pixel Template',
            'postbackTemplateName' => 'My Postback Template',
        ],
        'type' => 'CreateCampaign',
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
