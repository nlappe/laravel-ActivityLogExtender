<?php

namespace nlappe\LaravelActivityLogExtender\Traits;

use nlappe\LaravelActivityLogExtender\Constants\ActivityLogTopics;
use nlappe\LaravelActivityLogExtender\Constants\ActivityLogTypes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use ReflectionClass;
use ReflectionException;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Traits\LogsActivity;

trait ActivityTrait
{
    use LogsActivity;

    protected static $logName;

    public function getClass()
    {
        return new ReflectionClass(self::class);
    }

    /**
     * @return ActivityLogTypes
     */
    public static function getUseLogType()
    {
        $classNameParts = explode('\\', self::class);
        return ActivityLogTypes::make(strtoupper($classNameParts[count($classNameParts) - 1]) . '_LOG');
    }

    /**
     * @param ActivityLogTopics $topic
     * @param Carbon $from
     * @param Carbon $to
     * @return int
     */
    public static function getActivityTopicCountPerDateRange(ActivityLogTopics $topic, Carbon $from, Carbon $to)
    {
        return ActivityTrait::getActivityWhereBetween($topic, $from, $to)
            ->count();
    }

    /**
     * @param ActivityLogTopics $topic
     * @param Carbon $from
     * @param Carbon $to
     * @return Collection
     * @throws ReflectionException
     */
    public static function getActivityTopicPerDateRange(ActivityLogTopics $topic, Carbon $from, Carbon $to)
    {
        return ActivityTrait::getActivityWhereBetween($topic, $from, $to)
            ->get();
    }

    /**
     * @param ActivityLogTopics $topic
     * @param Carbon $from
     * @param Carbon $to
     * @return Builder
     */
    public static function getActivityWhereBetween(ActivityLogTopics $topic, Carbon $from, Carbon $to)
    {
        $from = isset($from) ? $from : Carbon::now()->subDays(7);
        $to = isset($to) ? $to : Carbon::now()->addDay(); //add one to include the submitted day
        return Activity::inLog(strtoupper(ActivityTrait::getUseLogType()->getValue()))
            ->where('description', $topic->getValue())
            ->whereBetween('created_at', [$from, $to]);
    }

    /**
     * @param ActivityLogTopics $topic
     * @return Builder
     */
    public function getActivitiesWhereTopic(ActivityLogTopics $topic)
    {
        return $this
            ->activities()
            ->where('description', $topic->getValue());
    }

    /**
     * @param ActivityLogTopics $topic
     * @return Builder
     */
    public function getActivityTopicSum(ActivityLogTopics $topic)
    {
        return $this
            ->getActivitiesWhereTopic($topic);
    }

    /**
     * @param ActivityLogTopics $topic
     * @return Model
     */
    public function getActivityLastTopic(ActivityLogTopics $topic)
    {
        return $this
            ->getActivitiesWhereTopic($topic)
            ->orderBy('created_at', 'desc')
            ->first();
    }

    /**
     * @param Model $target
     * @param Model $causer
     * @param ActivityLogTopics $topic
     */
    public static function log(Model $target, Model $causer, ActivityLogTopics $topic)
    {
        activity()
            ->performedOn($target)
            ->causedBy($causer)
            ->useLog(self::getUseLogType()->getValue())
            ->log($topic->getValue());
    }

    public function getLogNameToUse(): string
    {
        return isset(static::$logName) ? static::$logName : $this->getUseLogType();
    }
}
