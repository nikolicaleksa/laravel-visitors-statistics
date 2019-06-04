<?php

namespace Aleksa\LaravelVisitorsStatistics\Http\Middleware;

use Aleksa\LaravelVisitorsStatistics\Tracker;
use Closure;
use Illuminate\Http\Request;

class RecordVisits
{
    /**
     * @var Tracker
     */
    private $tracker;

    /**
     * RecordVisits constructor.
     *
     * @param Tracker $tracker
     */
    public function __construct(Tracker $tracker)
    {
        $this->tracker = $tracker;
    }

    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $this->tracker->recordVisit();

        return $next($request);
    }
}
