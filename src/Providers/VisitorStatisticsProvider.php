<?php

namespace Aleksa\LaravelVisitorsStatistics\Providers;

use Aleksa\LaravelVisitorsStatistics\Console\Commands\UpdateMaxMindDatabase;
use Aleksa\LaravelVisitorsStatistics\GeoIP;
use Aleksa\LaravelVisitorsStatistics\Http\Middleware\RecordVisits;
use Aleksa\LaravelVisitorsStatistics\Visitor;
use DeviceDetector\DeviceDetector;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class VisitorStatisticsProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register()
    {
        $this->app->bind(
            'Aleksa\LaravelVisitorsStatistics\Contracts\Tracker',
            'Aleksa\LaravelVisitorsStatistics\Tracker'
        );
        $this->app->bind('Aleksa\LaravelVisitorsStatistics\Contracts\Visitor', function ($app, $parameters) {
            return new Visitor($parameters['ipAddress'], $parameters['userAgent'], new DeviceDetector());
        });
        $this->app->bind('Aleksa\LaravelVisitorsStatistics\Contracts\GeoIP', function ($app, $parameters) {
            return new GeoIP($parameters['ipAddress']);
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot()
    {
        // Register config
        $this->publishes([
            __DIR__ . '/../../config/visitorstatistics.php' => config_path('visitorstatistics.php'),
        ], 'config');
        $this->mergeConfigFrom(
            __DIR__ . '/../../config/visitorstatistics.php',
            'visitorstatistics'
        );

        // Register routes
        $this->mapStatisticsRoutes();

        // Register migrations
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        // Register commands and set task scheduling
        $this->commands([
            UpdateMaxMindDatabase::class
        ]);
        $this->app->booted(function () {
            // Since maxmind database is updated every first Thursday of the month
            // day 12 of each month is guaranteed to be on or after first Thursday
            $schedule = app(Schedule::class);
            $schedule->command(UpdateMaxMindDatabase::class, ['scheduled' => true])->monthlyOn(12, '00:00');
        });

        // Register middleware and add it to 'web' group
        app('router')->pushMiddlewareToGroup('web', RecordVisits::class);
    }

    /**
     * Define routes for getting statistics data.
     *
     * @return void
     */
    private function mapStatisticsRoutes()
    {
        $config = config('visitorstatistics');

        Route::prefix($config['prefix'])
            ->middleware($config['middleware'])
            ->namespace('Aleksa\LaravelVisitorsStatistics\Http\Controllers')
            ->group(__DIR__ . '/../routes/web.php');
    }
}
