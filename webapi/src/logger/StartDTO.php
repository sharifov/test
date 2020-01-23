<?php

namespace webapi\src\logger;

use yii\base\BaseObject;

/**
 * Class StartDTO
 *
 * @property $data
 * @property $action
 * @property $userId
 * @property $ip
 * @property $startTime
 * @property $startMemory
 */
class StartDTO extends BaseObject
{
    public $data;
    public $action;
    public $userId;
    public $ip;
    public $startTime;
    public $startMemory;
}
