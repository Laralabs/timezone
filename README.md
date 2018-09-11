<p align="center">
    <img src="https://assets.laralabs.uk/packages/timezone/timezone_logo.png" height="92px" width="408px" />
</p>
<p align="center">
<a href="https://packagist.org/packages/laralabs/timezone"><img src="https://poser.pugx.org/laralabs/timezone/version" alt="Stable Build" /></a>
<a href="https://travis-ci.org/Laralabs/timezone"><img src="https://travis-ci.org/Laralabs/timezone.svg?branch=master" alt="Build Status"></a>
<a href="https://styleci.io/repos/142464749"><img src="https://styleci.io/repos/142464749/shield?branch=master" alt="StyleCI"></a>
<a href="https://codeclimate.com/github/Laralabs/timezone/maintainability"><img src="https://api.codeclimate.com/v1/badges/8112c5d1026cf4a01570/maintainability" /></a>
<a href="https://codeclimate.com/github/Laralabs/timezone/test_coverage"><img src="https://api.codeclimate.com/v1/badges/8112c5d1026cf4a01570/test_coverage" /></a>
</p>

The timezone package for Laravel provides an easy bi-directional conversion of DateTime into a variety of formats and locales.

## :rocket: Quick Start

### Installation
Require the package in the `composer.json` of your project.
```
composer require laralabs/timezone
```
Publish the configuration file.
```
php artisan vendor:publish --tag=timezone-config
```
Edit the configuration file and set your desired default display timezone and format.

### Usage
A helper function and facade is available, choose your preferred method.
For the following examples the default timezone will be `Europe/London` and `d/m/Y H:i:s` as the default format.

**Converting from storage**
```php
$date = '2018-09-11 11:00:00';
$result = timezone()->fromStorage($date);
$result->formatToDefault();

Output: 11/09/2018 12:00:00
```

**Converting to storage**
```php
$date = '11/09/2018 12:00:00';
$result = timezone()->toStorage($date);

Output: 2018-09-11 11:00:00
```

The package will check for a `timezone` key in the session before defaulting to the configuration value, alternatively it is possible to override the timezone with a second argument.

**Overriding timezone**
```php
$toTimezone = 'Europe/London';
timezone()->fromStorage($date, $toTimezone);

$fromTimezone = 'Europe/London';
timezone()->toStorage($date, $fromTimezone);
```

**Model Presenter**

The package also comes with a presenter that can be added to models as a trait, for more information on this see the full documentation available below.

## :orange-book: Documentation
Full documentation can be found [on the website](https://docs.laralabs.uk/timezone/).

## :speech-balloon: Support
Please raise an issue on GitHub if there is a problem.

## :key: License
This is open-sourced software licensed under the [MIT License](http://opensource.org/licenses/MIT).
