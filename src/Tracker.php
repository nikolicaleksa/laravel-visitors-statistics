<?php

namespace Aleksa\LaravelVisitorsStatistics;

use Aleksa\LaravelVisitorsStatistics\Contracts\Tracker as TrackerContract;
use Aleksa\LaravelVisitorsStatistics\Contracts\Visitor as VisitorContact;
use Aleksa\LaravelVisitorsStatistics\Models\Statistic;
use Aleksa\LaravelVisitorsStatistics\Models\Visitor as VisitorModel;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Http\Request;

class Tracker implements TrackerContract
{
    /**
     * @var Visitor
     */
    private $visitor;

    /**
     * @var Request
     */
    private $request;

    /**
     * @var CarbonInterface|static
     */
    private $today;

    /**
     * Tracker constructor.
     *
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->visitor = resolve(VisitorContact::class, [
            'ipAddress' => $this->request->header('HTTP_CF_CONNECTING_IP') ?? $this->request->getClientIp(),
            'userAgent' => $this->request->userAgent()
        ]);
        $this->today = Carbon::today();
    }

    /**
     * Save visitor information in the database.
     */
    public function recordVisit()
    {
        if ($this->shouldTrackUser()) {
            $isNewVisitor = $this->saveVisitor($this->getVisitorInformation());
            $this->updateStatistics();

            if ($isNewVisitor) {
                $this->updateUniqueStatistics();
            }

            $this->updateMaxOnline();
        }
    }

    /**
     * Check if the visitor should be tracked.
     *
     * @return bool
     */
    public function shouldTrackUser(): bool
    {
        if ((config('visitorstatistics.track_authenticated_users') === false && !is_null(auth()->user())) ||
            (config('visitorstatistics.track_ajax_request') === false && request()->ajax()) ||
            $this->request->is(config('visitorstatistics.login_route_path')) ||
            $this->visitor->isBot()) {
            return false;
        }

        return true;
    }

    /**
     * Gather visitor information.
     *
     * @return array
     */
    public function getVisitorInformation(): array
    {
        return [
            'ip' => $this->visitor->getIp(),
            'country' => $this->visitor->getCountry(),
            'city' => $this->visitor->getCity(),
            'device' => $this->visitor->getDevice(),
            'browser' => $this->visitor->getBrowser(),
        ];
    }

    /**
     * Save visitor information in database.
     *
     * @param array $visitorInformation
     *
     * @return bool True if new visitor, false if existing
     */
    private function saveVisitor(array $visitorInformation): bool
    {
        $hasVisitedToday = VisitorModel::where('ip', $visitorInformation['ip'])
            ->whereBetween('created_at', [$this->today, $this->today->copy()->endOfDay()])
            ->first();

        if ($hasVisitedToday) {
            $hasVisitedToday->touch();

            return false;
        }

        VisitorModel::create($visitorInformation);

        return true;
    }

    /**
     * Update statistics in the database.
     */
    private function updateStatistics(): void
    {
        $rowName = sprintf('%s_%s', $this->today->format('Y_m_d'), Statistic::TYPES['all']);
        $statistic = Statistic::firstOrNew([
            'name' => $rowName,
            'type' => Statistic::TYPES['all'],
        ]);
        $statistic->value++;
        $statistic->save();
    }

    /**
     * Update unique statistics in the database.
     */
    private function updateUniqueStatistics(): void
    {
        $rowName = sprintf('%s_%s', $this->today->format('Y_m_d'), Statistic::TYPES['unique']);
        $statistic = Statistic::firstOrNew([
            'name' => $rowName,
            'type' => Statistic::TYPES['unique'],
        ]);
        $statistic->value++;
        $statistic->save();
    }

    /**
     * Update max visitors online in the database.
     */
    private function updateMaxOnline()
    {
        $max = Statistic::maxVisitors();

        $endDate = Carbon::now();
        $startDate = $endDate->copy()->subMinutes(15);

        $currentMax = VisitorModel::whereBetween('updated_at', [$startDate, $endDate])->count();

        if ($currentMax > $max) {
            Statistic::updateOrCreate([
                'name' => 'max_online',
                'type' => Statistic::TYPES['max'],
            ], [
                'value' => $currentMax,
            ]);
        }
    }
}
