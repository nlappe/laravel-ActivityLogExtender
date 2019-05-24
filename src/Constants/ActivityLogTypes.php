<?php

namespace nlappe\LaravelActivityLogExtender\Constants;

use Spatie\Enum\Enum;

/**
 * @method static self user_log()
 */
class ActivityLogTypes extends Enum
{
    public const USER_LOG = 'user_log';
}
