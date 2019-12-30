<?php

namespace webapi\src\logger;

use yii\base\BaseObject;

/**
 * Class EndDTO
 *
 * @property $result
 * @property $endTime
 * @property $endMemory
 * @property $profiling
 */
class EndDTO extends BaseObject
{
    public $result;
    public $endTime;
    public $endMemory;
    public $profiling;
}
