# Laravel Visitor Statistics

[![Packagist](https://img.shields.io/packagist/v/aleksa/laravel-visitors-statistics.svg)](https://packagist.org/packages/aleksa/laravel-visitors-statistics) [![Packagist](https://img.shields.io/packagist/dm/aleksa/laravel-visitors-statistics.svg)](https://packagist.org/packages/aleksa/laravel-visitors-statistics) [![Packagist](https://img.shields.io/packagist/l/aleksa/laravel-visitors-statistics.svg)](https://opensource.org/licenses/MIT)

Simple visitor tracker and statistics package for Laravel 5 that can be used for dashboard graphs. Includes controller and routes to fetch visitor statistics (all and unique visits) for a certain month or year. You can also get total number of visits per country.

## Installation
1) Install package using composer:

```bash
composer require aleksa/laravel-visitors-statistics
```
2) Since the package automatically adds it's middleware to `web` group you will have to register service provider manually

```php
...
'providers' => [
    ...
    Aleksa\LaravelVisitorsStatistics\Providers\VisitorStatisticsProvider::class,
    ...
],
...
```

3) Run migrations:

```bash
php artisan migrate
```

4) Publish configuration:

```bash
php artisan vendor:publish
```
and choose `Aleksa\LaravelVisitorsStatistics\Providers\VisitorStatisticsProvider` from the list

5) Download MaxMind database
```bash
php artisan maxmind:update
```

## GeoIP

Since fetching data from external API (eg: [ipstack](https://ipstack.com/), [ipdata](https://ipdata.co/) etc...) takes time and slows down your application and can also produce monthly costs the package uses local MaxMind database and `maxmind-db/reader` package for reading it's contents and locating the visitors.

For more sophisticated tracking you should use something like [Google Analytics](https://analytics.google.com/).

## Configuration

| Name | Description | Default |
| --- | --- | --- |
| track_authenticated_users | Should the tracker track authenticated users | false |
| track_ajax_request | Should the tracker track ajax requests | false |
| login_route_path | Admin login path so that login attempts don't track as visits | 'admin' |
| prefix | Prefix to apply to all statistics fetching routes | 'admin' |
| middleware | Middlewares to be applied to all statistics fetching routes | '['web', 'auth']' |
| database_location | Location where to store MaxMind database | storage_path('app/maxmind.mmdb') |
| database_download_url | MaxMind database download url | MAXMIND_URL |
| auto_update | Should laravel automatically update MaxMind database | true |

**NOTE:** If you set `auto_update` to true make sure to add Laravel cron entry that is needed for [Task Scheduling](https://laravel.com/docs/5.8/scheduling).

## Fetching statistics

The package comes with a controller and a bunch of routes to fetch statistics. The idea is to fetch statistics on your dashboard with AJAX request and parse data to some JavaScript graph library like [Highcharts](https://www.highcharts.com/).

| Route name | Route URI | Description |
| --- | --- | --- |
| visitorstatistics.all_statistics | /statistics/{year}/{month?} | Get statistics for the given year or month. |
| visitorstatistics.unique_statistics | /statistics/unique/{year}/{month?} | Get unique statistics for the given year or month. |
| visitorstatistics.total_statistics | /statistics/total/{year}/{month?} | Get both all and unique statistics for a given year or month. |
| visitorstatistics.countries | /statistics/countries | Get visits count for each country. |
| visitorstatistics.available_dates | /statistics/available/{year?} | Get years or months that have statistics tracked. |

**NOTE:** All routes are prefixed with value set in configuration and return response in `JSON` format.

## Example responses

`/admin/statistics/2019`

```json
{
    "data": {
        "1": 712,
        "2": 1379,
        "3": 1095,
        "4": 624,
        "5": 1181,
        "6": 271,
        "7": 0,
        "8": 0,
        "9": 0,
        "10": 0,
        "11": 0
    }
}
```

`/admin/statistics/2019/6`

```json
{
    "data": {
        "1": 76,
        "2": 33,
        "3": 35,
        "4": 54,
        "5": 73,
        "6": 0,
        "7-26": "...",
        "27": 0,
        "28": 0,
        "29": 0
    }
}
```

`/admin/statistics/total/2019`

```json
{
    "all": {
        "1": 0,
        "2": 0,
        "3": 0,
        "4": 0,
        "5": 0,
        "6": 271,
        "7": 0,
        "8": 0,
        "9": 0,
        "10": 0,
        "11": 0
    },
    "unique": {
        "1": 0,
        "2": 0,
        "3": 0,
        "4": 0,
        "5": 0,
        "6": 42,
        "7": 0,
        "8": 0,
        "9": 0,
        "10": 0,
        "11": 0
    }
}
```

`/admin/statistics/countries`
```json
{
    "data": [
        {
            "country": "Unknown",
            "visitor_count": 13
        },
        {
            "country": "Serbia",
            "visitor_count": 2454
        },
        {
            "country": "Russia",
            "visitor_count": 1874
        },
        {
            "country": "Germany",
            "visitor_count": 2002
        }
    ]
}
```

`/admin/statistics/available`
```json
{
    "data": [
        2019
    ]
}
```

## Information being collected

This is the data that is being tracked for each visitor.

| Name | Description |
| --- | --- |
| ip | e.g. '127.0.0.1' |
| country | e.g. 'Serbia' |
| city | e.g. 'Belgrade' |
| device | e.g. 'desktop' |
| browser | e.g. 'Chrome' |

## License

This is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT).