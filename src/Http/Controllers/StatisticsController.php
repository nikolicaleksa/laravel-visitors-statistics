<?php

namespace Aleksa\LaravelVisitorsStatistics\Http\Controllers;

use Aleksa\LaravelVisitorsStatistics\Models\Visitor;
use Aleksa\LaravelVisitorsStatistics\Models\Statistic;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;

class StatisticsController extends Controller
{
    /**
     * Get statistics for the given year or month.
     *
     * @param int $year
     * @param int|null $month
     *
     * @return JsonResponse
     */
    public function getStatistics(int $year, ?int $month = null): JsonResponse
    {
        return response()->json([
            'data' => $this->retrieveStatistics(Statistic::TYPES['all'], $year, $month),
        ]);
    }

    /**
     * Get unique statistics for the given year or month.
     *
     * @param int $year
     * @param int|null $month
     *
     * @return JsonResponse
     */
    public function getUniqueStatistics(int $year, ?int $month = null): JsonResponse
    {
        return response()->json([
            'data' => $this->retrieveStatistics(Statistic::TYPES['unique'], $year, $month),
        ]);
    }

    /**
     * Get both all and unique statistics for a given year or month.
     *
     * @param int $year
     * @param int|null $month
     *
     * @return JsonResponse
     */
    public function getTotalStatistics(int $year, ?int $month = null): JsonResponse
    {
        return response()->json([
            'all' => $this->retrieveStatistics(Statistic::TYPES['all'], $year, $month),
            'unique' => $this->retrieveStatistics(Statistic::TYPES['unique'], $year, $month),
        ]);
    }

    /**
     * Get visits count and percentage for each country.
     *
     * @return JsonResponse
     */
    public function getCountriesStatistics(): JsonResponse
    {
        $visitors = Visitor::getVisitorCountPerCountry();
        $visitorCount = Visitor::count();

        foreach ($visitors as $visitor) {
            $visitor->percentage = round($visitor->count * 100 / $visitorCount, 2);
        }

        return response()->json([
            'data' => $visitors,
        ]);
    }

    /**
     * Get years or months that have statistics tracked.
     *
     * @param int|null $year
     *
     * @return JsonResponse
     */
    public function getAvailableDates(?int $year = null): JsonResponse
    {
        $result = [];

        if (is_null($year)) {
            $min = Statistic::min('created_at');
            $max = Statistic::max('created_at');

            if (!is_null($min)) {
                $startYear = Carbon::createFromTimeString($min)->year;
                $endYear = Carbon::createFromTimeString($max)->year;

                for ($i = $startYear; $i <= $endYear; $i++) {
                    $result[] = $i;
                }

                if ($startYear !== $endYear) {
                    $result[] = $endYear;
                }
            }
        } else {
            $startDate = Carbon::createFromDate($year, 1, 1);
            $endDate = Carbon::createFromDate($year, 12, 31);

            $min = Statistic::whereBetween('created_at', [$startDate, $endDate])->min('created_at');
            $max = Statistic::whereBetween('created_at', [$startDate, $endDate])->max('created_at');

            if (!is_null($min)) {
                $startMonth = Carbon::createFromTimeString($min)->month;
                $endMonth = Carbon::createFromTimeString($max)->month;

                for ($i = $startMonth; $i <= $endMonth; $i++) {
                    $result[] = $i;
                }
            }
        }

        return response()->json([
            'data' => $result
        ]);
    }

    /**
     * Retrieve statistics for given year or month and type.
     *
     * @param string $type
     * @param int $year
     * @param int|null $month
     *
     * @return array
     */
    private function retrieveStatistics(string $type, int $year, ?int $month = null): array
    {
        if (is_null($month)) {
            $startDate = Carbon::createFromDate($year, 1, 1)->startOfDay();
            $endDate = $startDate->copy()->endOfYear();
        } else {
            $startDate = Carbon::createFromDate($year, $month, 1)->startOfDay();
            $endDate = $startDate->copy()->endOfMonth();
        }

        $data = [];
        $statistics = Statistic::select(['value', 'created_at'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->where('type', $type)
            ->get();

        if (is_null($month)) {
            for ($i = 1; $i <= 12; $i++) {
                $data[$i] = 0;
            }

            foreach ($statistics as $statistic) {
                $data[Carbon::createFromTimeString($statistic->created_at)->month] += $statistic->value;
            }
        } else {
            for ($i = 1; $i <= Carbon::createFromDate($year, $month, 1)->endOfMonth()->day; $i++) {
                $data[$i] = 0;
            }

            foreach ($statistics as $statistic) {
                $data[Carbon::createFromTimeString($statistic->created_at)->day] += $statistic->value;
            }
        }

        return $data;
    }
}
