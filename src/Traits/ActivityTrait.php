<?php

namespace nlappe\LaravelActivityLogExtender\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\ActivitylogServiceProvider;

trait ActivityTrait
{
    use LogsActivity;
    /**
     * @return string
     */
    public static function getActivityModelClass()
    {
        return ActivitylogServiceProvider::determineActivityModel();
    }

    /**
     * @return string
     */
    public static function getLogNameToUse()
    {
        return strtoupper(class_basename(static::class)) . '_LOG';
    }

    /**
     * @param string $topic
     * @param Carbon $from
     * @param Carbon $to
     * @return int
     */
    public static function getActivityTopicCountPerDateRange(string $topic, Carbon $from = null, Carbon $to = null)
    {
        return static::activityTopicWhereBetween($topic, $from, $to)
            ->count();
    }

    /**
     * @param string $topic
     * @param Carbon $from
     * @param Carbon $to
     * @return Collection
     */
    public static function getActivityTopicWhereBetween(string $topic, Carbon $from = null, Carbon $to = null)
    {
        return static::activityTopicWhereBetween($topic, $from, $to)
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
        return static::getActivityModelClass()::inLog(strtolower(static::getLogNameToUse()))
            ->where('description', $topic)
            ->whereBetween('created_at', [$from, $to]);
    }

    /**
     * @param string $topic
     * @return Builder
     */
    public function activitiesWhereTopic(string $topic)
    {
        return $this
            ->activities()
            ->where('description', $topic);
    }

    /**
     * @param string $topic
     * @return Builder
     */
    public function getActivitySumByTopic(string $topic)
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
            ->useLog(static::getLogNameToUse())
            ->log($topic);
    }
}
