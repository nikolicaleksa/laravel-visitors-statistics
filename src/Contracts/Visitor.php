<?php

namespace Aleksa\LaravelVisitorsStatistics\Contracts;

interface Visitor
{
    /**
     * Get visitor IP.
     *
     * @return string
     */
    public function getIp(): string;

    /**
     * Get visitor country name.
     *
     * @return string
     */
    public function getCountry(): string;

    /**
     * Get visitor city name.
     *
     * @return string
     */
    public function getCity(): string;

    /**
     * Get visitor device name.
     *
     * @return string
     */
    public function getDevice(): string;

    /**
     * Get visitor browser name.
     *
     * @return string
     */
    public function getBrowser(): string;

    /**
     * Check whether the visitor is a bot.
     *
     * @return bool
     */
    public function isBot(): bool;
}
