<?php
return [
    /*
     * This model will be used for the Enum-Typing.
     * It should be implements the extend the Constants\ActivityLogTopics and Constants\ActivityLogTypes classes
     */
    'activity_log_topics_model' => \nlappe\LaravelActivityLogExtender\Constants\ActivityLogTopics::class,
    'activity_log_types_model' => \nlappe\LaravelActivityLogExtender\Constants\ActivityLogTypes::class,
];
