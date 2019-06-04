<?php

namespace Aleksa\LaravelVisitorsStatistics\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * App\Statistic
 *
 * @property int $id
 * @property string $name
 * @property int $value
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|Statistic newModelQuery()
 * @method static Builder|Statistic newQuery()
 * @method static Builder|Statistic query()
 * @method static Builder|Statistic whereCreatedAt($value)
 * @method static Builder|Statistic whereId($value)
 * @method static Builder|Statistic whereName($value)
 * @method static Builder|Statistic whereUpdatedAt($value)
 * @method static Builder|Statistic whereValue($value)
 * @mixin Eloquent
 */
class Statistic extends Model
{
    public const TYPES = [
        'all' => 'all',
        'unique' => 'unique',
        'max' => 'max',
    ];


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'value'
    ];

    /**
     * Get the number of max visits for a certain period.
     *
     * @return int
     */
    public static function maxVisitors(): int
    {
        $max = Statistic::select(['value'])->where('type', self::TYPES['max'])->first();

        return $max->value ?? 0;
    }
}
