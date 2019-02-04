# IN10 Multilanguage
> Simple, opinionated multilanguage package for Laravel

## Contents
- [Requirements](#requirements)
- [Design](#design)
- [Installation](#installation)
- [Migrating from Arcanedev/localization](#migrating-from-arcanedevlocalization)
- [Usage](#usage)
- [Developer](#developer)
- [License](#license)

## Requirements
* PHP 7.1 - 7.3
* Laravel 5.7

## Design
This is an opinionated package: it works in a specific way, based on the setups we run at IN10. That means:

1. All translated routes start with a route part, e.g. example.com/de/news/
1. A website has a pre-defined set of languages, all other languages return a 404.
1. A language is always two characters.
1. The website has a single default language, by default "en" which you can change in the configuration. This default language is excluded from the URL via a 301-redirect. If you visit example.com/en/test, it will be redirected to example.com/test.

## Installation
Install the package using composer:
```bash
composer require "in10/multilanguage"
```
and publish the configuration file:
```bash
php artisan vendor:publish --provider=IN10\\Multilanguage\\ServiceProvider
```
You can customize this file as needed.

## Migrating from ARCANEDEV/localization
One of the packages we used to use at IN10 is [ARCANEDEV/Localization](https://github.com/arcanedev/localization). To facilitate an easy upgrade from the package to this new, smaller package, execute the following steps:

1. Remove the existing package: `composer remove arcanedev/localization`.
1. Remove the ServiceProvider and configuration file if needed.
1. Find every instance of `localization()->getCurrentLocale()` and replace it with a call to `App::getLocale()`.
1. Follow the steps in the [Installation](#installation) section above to install IN10/multilanguage.

## Usage
You can make a set of routes translated by wrapping them in a group:
```php
Route::multilanguage([], function() {
    Route::get('/', 'HomepageController')->name('homepage');
    Route::get('/news/{slug}', 'HomepageController')->name('news.show');
});
```
The first parameter `attributes` takes the same settings as a regular route group, except for `prefix`, `as` and `middleware`, which are overwritten (these parameters are required to make the translation work). The multilanguage-group should be a root-level construct, adding it inside of another group or prefix is not tested, and would probably not work.

If you want to generate a route with a correct language, use the included helper:
```php
function translatedRoute(string $route, array $parameters = [], bool $absolute = true, ?string $language = null) : string
```
This helpers takes the same parameters as the Laravel `route()` helper, with an optional language as a last parameter. If you omit the language, the helper uses the current language for the request. This is usually what you want, so in general you can use the translatedRoute helper as if it where the regular helper:
```php
translatedRoute('news.show', ['slug' => 'five-ways-to-translate-content');
```
which will generate `/nl/news/five-ways-to-translate-content` in this example if the current language is set to Dutch.

## Developer
[Jakob Buis](https://www.jakobbuis.nl)

## License
Copyright 2019 [IN10](https://www.in10.nl). All rights reserved.
