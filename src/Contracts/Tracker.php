<?php

namespace Aleksa\LaravelVisitorsStatistics\Contracts;

interface Tracker
{
    /**
     * Save visitor information in the database.
     */
    public function recordVisit();

    /**
     * Check if the visitor should be tracked.
     *
     * @return bool
     */
    public function shouldTrackUser(): bool;

    /**
     * Gather visitor information.
     *
     * @return array
     */
    public function getVisitorInformation(): array;
}
