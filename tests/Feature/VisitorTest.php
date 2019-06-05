<?php

namespace Aleksa\LaravelVisitorsStatistics\Tests\Feature;

use Aleksa\LaravelVisitorsStatistics\Contracts\Visitor as VisitorContact;
use Aleksa\LaravelVisitorsStatistics\Tests\TestCase;

class VisitorTest extends TestCase
{
    private const DEFAULT_IP_ADDRESS = '127.0.0.1';

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
        $visitor = $this->app->makeWith(VisitorContact::class, [
            'ipAddress' => self::DEFAULT_IP_ADDRESS,
            'userAgent' => $userAgent
        ]);

        // Check browser and device
        $this->assertEquals($expectedBrowser, $visitor->getBrowser());
        $this->assertEquals($expectedDevice, $visitor->getDevice());
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