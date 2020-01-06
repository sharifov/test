<?php

namespace webapi\src\response\messages;

use yii\base\Arrayable;
use yii\base\ArrayableTrait;
use yii\base\BaseObject;

/**
 * Class MessageValue
 *
 * @property $data
 */
abstract class MessageValue extends BaseObject implements Arrayable
{
    use ArrayableTrait;

    public function getData()
    {
        return $this->toArray();
    }
}
