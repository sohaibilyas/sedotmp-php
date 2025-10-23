# SedoTMP PHP SDK

A simple PHP SDK for interacting with SedoTMP - Sedo Traffic Monetization Platform (sedotmp.com).

> **Requires [PHP 8.2+](https://php.net/releases/)**

## Installation

Install via [Composer](https://getcomposer.org):

```bash
composer require sohaibilyas/sedotmp-php
```

## Usage

```php
use SohaibIlyas\SedoTmp\SedoTmp;

$client = new SedoTmp('your-client-id', 'your-client-secret');

$accessToken = $client->authenticate();
```

### Custom URLs

You can optionally provide custom base URL and auth URL:

```php
$client = new SedoTmp(
    'your-client-id',
    'your-client-secret',
    'https://custom-api.sedotmp.com',
    'https://custom-auth.sedotmp.com/oauth/token'
);
```

## Development

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

Run unit tests using **PEST**:
```bash
composer test:unit
```

Run the entire test suite:
```bash
composer test
```

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
