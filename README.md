# Filament Check Whois Widget

[![Latest Version on Packagist](https://img.shields.io/packagist/v/jeffersongoncalves/filament-check-whois-widget.svg?style=flat-square)](https://packagist.org/packages/jeffersongoncalves/filament-check-whois-widget)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/jeffersongoncalves/filament-check-whois-widget/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/jeffersongoncalves/filament-check-whois-widget/actions?query=workflow%3A"Fix+PHP+code+styling"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/jeffersongoncalves/filament-check-whois-widget.svg?style=flat-square)](https://packagist.org/packages/jeffersongoncalves/filament-check-whois-widget)

The Filament Check Whois Widget is a package for the Filament PHP framework that allows you to easily check the WHOIS information for domains. It integrates seamlessly with Filament's AdminPanel, providing a clean and user-friendly interface. The widget fetches WHOIS data using an external API (requiring an API key), displaying key details like registrant information, registration date, and expiry date. Configuration options allow for customization of the widget's appearance and behavior, such as setting the number of domains displayed per row, the column span, and whether to show a title. This simplifies the process of obtaining crucial domain information within your Filament application.

## Installation

You can install the package via composer:

```bash
composer require jeffersongoncalves/filament-check-whois-widget
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="filament-check-whois-widget-config"
```

Optionally, you can publish the views using

```bash
php artisan vendor:publish --tag="filament-check-whois-widget-views"
```

This is the contents of the published config file:

```php
return [
    'ip2_whois_api_key' => env('CHECK_WHOIS_API_KEY'),
];
```

## Usage
Add in AdminPanelProvider.php

```php
use JeffersonGoncalves\FilamentCheckWhoisWidget\FilamentCheckWhoisWidgetPlugin;

->plugins([
    FilamentCheckWhoisWidgetPlugin::make()
        ->domains([
            'filamentphp.com'
        ])
])
```

Optionally, you can add more configs as example below:

```php
use JeffersonGoncalves\FilamentCheckWhoisWidget\FilamentCheckWhoisWidgetPlugin;

FilamentCheckWhoisWidgetPlugin::make()
    ->domains([
        'filamentphp.com'
    ])
    ->shouldShowTitle(false) // Optional show title default is: true
    ->setTitle('Whois') // Optional
    ->setDescription('Whois detail')  // Optional
    ->setQuantityPerRow(1) //Optional quantity per row default is: 1
    ->setColumnSpan('full') //Optional column span default is: '1/2' 
    ->setSort(10)
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Jèfferson Gonçalves](https://github.com/jeffersongoncalves)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
