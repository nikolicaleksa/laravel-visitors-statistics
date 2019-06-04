<?php

namespace Aleksa\LaravelVisitorsStatistics;

use Aleksa\LaravelVisitorsStatistics\Contracts\Visitor as VisitorContract;
use DeviceDetector\DeviceDetector;
use Illuminate\Http\Request;

class Visitor implements VisitorContract
{
    /**
     * @var string
     */
    protected $ipAddress;

    /**
     * @var Request
     */
    private $request;

    /**
     * @var GeoIP
     */
    private $geoIP;

    /**
     * @var DeviceDetector
     */
    private $deviceDetector;

    /**
     * Visitor constructor.
     *
     * @param Request $request
     * @param DeviceDetector $deviceDetector
     */
    public function __construct(Request $request, DeviceDetector $deviceDetector)
    {
        $this->request = $request;

        $this->ipAddress = $this->request->header('HTTP_CF_CONNECTING_IP') ?? $this->request->getClientIp();
        $this->geoIP = resolve('Aleksa\LaravelVisitorsStatistics\GeoIP', [
            'ipAddress' => $this->ipAddress
        ]);

        $this->deviceDetector = $deviceDetector;
        $this->deviceDetector->setUserAgent($request->userAgent());
        $this->deviceDetector->parse();
    }

    /**
     * Get visitor IP.
     *
     * @return string
     */
    public function getIp(): string
    {
        return $this->ipAddress;
    }

    /**
     * Get visitor country name.
     *
     * @return string
     */
    public function getCountry(): string
    {
        return $this->geoIP->getCountry();
    }

    /**
     * Get visitor city name.
     *
     * @return string
     */
    public function getCity(): string
    {
        return $this->geoIP->getCity();
    }

    /**
     * Get visitor device name.
     *
     * @return string
     */
    public function getDevice(): string
    {
        return $this->deviceDetector->getDeviceName();
    }

    /**
     * Get visitor browser name.
     *
     * @return string
     */
    public function getBrowser(): string
    {
        return $this->deviceDetector->getClient('name');
    }

    /**
     * Check whether the visitor is a bot.
     *
     * @return bool
     */
    public function isBot(): bool
    {
        return $this->deviceDetector->isBot();
    }
}
