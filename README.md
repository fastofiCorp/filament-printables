# Filament Printables: a package to generate reports and form printables for your app.

[![Latest Version on Packagist](https://img.shields.io/packagist/v/fastofi-corp/filament-printables.svg?style=flat-square)](https://packagist.org/packages/fastofi-corp/filament-printables)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/fastofiCorp/filament-printables/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/fastofiCorp/filament-printables/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/fastofiCorp/filament-printables/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/fastofiCorp/filament-printables/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/fastofi-corp/filament-printables.svg?style=flat-square)](https://packagist.org/packages/fastofi-corp/filament-printables)

<!--delete-->

This is a work in progress thing

## Installation

You can install the package via composer:

```bash
composer require fastoficorp/filament-printables
```

You can publish and run the migrations with:

```bash
php artisan vendor:publish --tag="filament-printables-migrations"
php artisan migrate
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="filament-printables-config"
```

## Usage

### Create Templates in the Templates Resource

Work in Progress

### Use the Buttons in your tables

To use the button in your table, just add the following lines:

```php

// For Single Actions
FastofiCorp\FilamentPrintables\Actions\PrintAction::make(),

// For Bulk Actions
FastofiCorp\FilamentPrintables\Actions\BulksPrintAction::make(),
```

Feel free to use all the actions methods for Filament Actions (we suggest not to override 'action()' and 'forms()' methods because this is where the plugin works).

## Testing

Work in progress

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

-   [Alvaro León Torres](https://github.com/alvleont)
-   [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
