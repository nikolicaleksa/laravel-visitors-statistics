<?php

namespace Aleksa\LaravelVisitorsStatistics;

use Aleksa\LaravelVisitorsStatistics\Contracts\GeoIP as GeoIPContract;
use Exception;
use Illuminate\Support\Facades\Log;
use MaxMind\Db\Reader;

class GeoIP implements GeoIPContract
{
    /**
     * @var string
     */
    private $country = 'Unknown';

    /**
     * @var string
     */
    private $city = 'Unknown';

    /**
     * GeoIP constructor.
     *
     * @param string $ipAddress
     */
    public function __construct(string $ipAddress)
    {
        try {
            $reader = new Reader(config('visitorstatistics.database_location'));
            $info = $reader->get($ipAddress);

            if (isset($info['country'], $info['city'])) {
                $this->country = $info['country']['names']['en'];
                $this->city = $info['city']['names']['en'];
            }
        } catch (Exception $ex) {
            Log::error($ex->getMessage());
        }
    }

    /**
     * Locate country for the set ip.
     *
     * @return string
     */
    public function getCountry(): string
    {
        return $this->country;
    }

    /**
     * Locate city for the set ip.
     *
     * @return string
     */
    public function getCity(): string
    {
        return $this->city;
    }
}
