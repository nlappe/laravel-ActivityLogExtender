<?php

namespace nlappe\LaravelActivityLogExtender\Constants;

use Spatie\Enum\Enum;

/**
 * @method static self created()
 * @method static self updated()
 * @method static self deleted()
 * @method static self login()
 * @method static self logout()
 * @method static self login_failed()
 */
class ActivityLogTopics extends Enum {}
