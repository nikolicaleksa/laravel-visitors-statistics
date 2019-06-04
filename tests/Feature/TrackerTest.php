<?php

namespace Aleksa\LaravelVisitorsStatistics\Tests\Feature;

use Aleksa\LaravelVisitorsStatistics\Http\Middleware\RecordVisits;
use Aleksa\LaravelVisitorsStatistics\Models\Statistic;
use Aleksa\LaravelVisitorsStatistics\Models\Visitor as VisitorModel;
use Aleksa\LaravelVisitorsStatistics\Tests\TestCase;
use Aleksa\LaravelVisitorsStatistics\Tracker;
use Aleksa\LaravelVisitorsStatistics\Visitor;
use DeviceDetector\DeviceDetector;
use Illuminate\Foundation\Auth\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

class TrackerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Route URL to register for testing purposes
     *
     * @var string
     */
    protected $routeUrl = '/visitors-statistics-test';

    /**
     * @inheritDoc
     */
    public function setUp(): void
    {
        parent::setUp();

        Route::get($this->routeUrl, function () {
            return 'Laravel Visitors Statistics test.';
        });
    }

    /**
     * Test if visit is being tracked.
     */
    public function testVisitorTracking()
    {
        // Make request
        $request = Request::create($this->routeUrl, 'GET');
        $tracker = $this->app->make(Tracker::class);
        $middleware = new RecordVisits($tracker);
        $middleware->handle($request, function () {
        });

        // Check if visit is tracked
        $this->assertEquals(1, Statistic::getTotalVisitors());

        // Check if visitor information is tracked
        $visitor = VisitorModel::first();

        $this->assertNotNull($visitor->ip);
        $this->assertEquals('Unknown', $visitor->country);
        $this->assertEquals('Unknown', $visitor->city);
    }

    /**
     * Test browser and device detection.
     *
     * @param string $userAgent
     * @param string $expectedBrowser
     * @param string $expectedDevice
     *
     * @dataProvider userAgentDataSet
     */
    public function testVisitorBrowserAndDevice($userAgent, $expectedBrowser, $expectedDevice)
    {
        // Make request
        $request = Request::create($this->routeUrl, 'GET');
        $request->headers->set('User-Agent', $userAgent);
        $visitor = new Visitor($request, new DeviceDetector());

        // Check browser and device
        $this->assertEquals($expectedBrowser, $visitor->getBrowser());
        $this->assertEquals($expectedDevice, $visitor->getDevice());
    }

    /**
     * Test tracking of authenticated users.
     */
    public function testDontTrackAuthenticatedUser()
    {
        // Check if user is not authenticated and do authentication
        $this->assertFalse(auth()->check());
        Auth::login(new User());
        $this->assertTrue(auth()->check());

        // Set tracking of authenticated users to false
        config(['visitorstatistics.track_authenticated_users' => false]);

        // Make request
        $request = Request::create($this->routeUrl, 'GET');
        $tracker = $this->app->make(Tracker::class);
        $middleware = new RecordVisits($tracker);
        $middleware->handle($request, function () {
        });

        // Check if visit is not tracked
        $this->assertEquals(0, Statistic::getTotalVisitors());
    }

    public function userAgentDataSet(): array
    {
        return [
            'Desktop Chrome' => [
                'userAgent' => 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/71.0.3578.80 Safari/537.36',
                'browser' => 'Chrome',
                'device' => 'desktop',
            ],
            'Desktop Firefox' => [
                'userAgent' => 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:58.0) Gecko/20100101 Firefox/58.0',
                'browser' => 'Firefox',
                'device' => 'desktop',
            ],
            'Android Chrome' => [
                'userAgent' => 'Mozilla/5.0 (Linux; Android 6.0.1; SM-G935S Build/MMB29K; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/55.0.2883.91 Mobile Safari/537.36',
                'browser' => 'Chrome Mobile',
                'device' => 'smartphone',
            ],
            'iPhone Safari' => [
                'userAgent' => 'Mozilla/5.0 (iPhone; CPU iPhone OS 11_0 like Mac OS X) AppleWebKit/604.1.38 (KHTML, like Gecko) Version/11.0 Mobile/15A356 Safari/604.1',
                'browser' => 'Mobile Safari',
                'device' => 'smartphone'
            ]
        ];
    }
}
