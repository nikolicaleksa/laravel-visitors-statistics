<?php

namespace Aleksa\LaravelVisitorsStatistics;

use Aleksa\LaravelVisitorsStatistics\Contracts\Visitor as VisitorContract;
use Aleksa\LaravelVisitorsStatistics\Contracts\GeoIP as GeoIPContract;
use DeviceDetector\DeviceDetector;

class Visitor implements VisitorContract
{
    /**
     * @var string
     */
    protected $ipAddress;

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
     * @param string $ipAddress
     * @param string $userAgent
     * @param DeviceDetector $deviceDetector
     */
    public function __construct(string $ipAddress, string $userAgent, DeviceDetector $deviceDetector)
    {
        $this->ipAddress = $ipAddress;
        $this->geoIP = resolve(GeoIPContract::class, [
            'ipAddress' => $this->ipAddress
        ]);

        $this->deviceDetector = $deviceDetector;
        $this->deviceDetector->setUserAgent($userAgent);
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
