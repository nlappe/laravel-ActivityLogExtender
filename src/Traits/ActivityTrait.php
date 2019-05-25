<?php

namespace nlappe\LaravelActivityLogExtender\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use ReflectionClass;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Traits\LogsActivity;

trait ActivityTrait
{
    use LogsActivity;
    /**
     * @return string
     */
    public static function getActivityModelClass()
    {
        return Activity::class;
    }

    /**
     * @return string
     */
    public static function getLogNameToUse()
    {
        $classNameParts = explode('\\', self::class);
        return strtoupper($classNameParts[count($classNameParts) - 1]) . '_LOG';
    }

    /**
     * @param string $topic
     * @param Carbon $from
     * @param Carbon $to
     * @return int
     */
    public static function getActivityTopicCountPerDateRange(string $topic, Carbon $from, Carbon $to)
    {
        return ActivityTrait::getActivityWhereBetween($topic, $from, $to)
            ->count();
    }

    /**
     * @param string $topic
     * @param Carbon $from
     * @param Carbon $to
     * @return Collection
     */
    public static function getActivityTopicPerDateRange(string $topic, Carbon $from, Carbon $to)
    {
        return ActivityTrait::getActivityWhereBetween($topic, $from, $to)
            ->get();
    }

    /**
     * @param string $topic
     * @param Carbon $from
     * @param Carbon $to
     * @return Builder
     */
    public static function activityTopicWhereBetween(string $topic, Carbon $from = null, Carbon $to = null)
    {
        $from = isset($from) ? $from : Carbon::now()->subDays(7);
        $to = isset($to) ? $to : Carbon::now()->addDay(); //add one to include the submitted day
        return static::getActivityModelClass()::inLog(strtoupper(ActivityTrait::getLogNameToUse()))
            ->where('description', $topic)
            ->whereBetween('created_at', [$from, $to]);
    }

    /**
     * @param string $topic
     * @return Builder
     */
    public function getActivitiesWhereTopic(string $topic)
    {
        return $this
            ->activities()
            ->where('description', $topic);
    }

    /**
     * @param string $topic
     * @return Builder
     */
    public function getActivityTopicSum(string $topic)
    {
        return $this
            ->getActivitiesWhereTopic($topic)
            ->count();
    }

    /**
     * @param string $topic
     * @return Model
     */
    public function getLastActivityByTopic(string $topic)
    {
        return $this
            ->getActivitiesWhereTopic($topic)
            ->orderBy('created_at', 'desc')
            ->first();
    }

    /**
     * @param Model $target
     * @param Model $causer
     * @param string $topic
     */
    public static function log(Model $target, Model $causer, string $topic)
    {
        activity()
            ->performedOn($target)
            ->causedBy($causer)
            ->useLog(ActivityTrait::getLogNameToUse())
            ->log($topic);
    }
}
