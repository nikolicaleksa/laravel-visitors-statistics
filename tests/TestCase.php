<?php

namespace Aleksa\LaravelVisitorsStatistics\Tests;

use Aleksa\LaravelVisitorsStatistics\Providers\VisitorStatisticsProvider;
use Orchestra\Testbench\TestCase as BaseTestCase;

class TestCase extends BaseTestCase
{
    /**
     * @inheritDoc
     */
    protected function getPackageProviders($app)
    {
        return [VisitorStatisticsProvider::class];
    }
}
