<?php

namespace Aleksa\LaravelVisitorsStatistics\Models;

use Carbon\Carbon;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * App\Visitor
 *
 * @property int $id
 * @property string $ip
 * @property string $country
 * @property string $city
 * @property string $device
 * @property string $browser
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static Builder|Visitor newModelQuery()
 * @method static Builder|Visitor newQuery()
 * @method static Builder|Visitor query()
 * @method static Builder|Visitor whereCity($value)
 * @method static Builder|Visitor whereCountry($value)
 * @method static Builder|Visitor whereCreatedAt($value)
 * @method static Builder|Visitor whereId($value)
 * @method static Builder|Visitor whereIp($value)
 * @method static Builder|Visitor whereUpdatedAt($value)
 * @mixin Eloquent
 * @method static Builder|Visitor whereBrowser($value)
 * @method static Builder|Visitor whereDevice($value)
 */
class Visitor extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'ip', 'country', 'city', 'device', 'browser'
    ];

    /**
     * Get visitor count for all visitor countries.
     *
     * @return Collection
     */
    public static function getVisitorCountPerCountry(): Collection
    {
        return Visitor::select(['country', DB::raw('COUNT(*) as visitor_count')])
            ->groupBy('country')
            ->get();
    }

    /**
     * Get the number of online users
     *
     * @param int $minutes
     *
     * @return int
     */
    public static function onlineCount(int $minutes = 15): int
    {
        $date = Carbon::now()->subMinutes($minutes);

        return Visitor::where('updated_at', '>=', $date)->count();
    }
}
