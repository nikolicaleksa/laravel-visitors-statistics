<?php

namespace Aleksa\LaravelVisitorsStatistics\Contracts;

interface GeoIP
{
    /**
     * Locate country for the set ip.
     *
     * @return string
     */
    public function getCountry(): string;

    /**
     * Locate city for the set ip.
     *
     * @return string
     */
    public function getCity(): string;
}
