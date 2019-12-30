<?php

namespace webapi\src\response;

use yii\base\BaseObject;

/**
 * Class DataResponse
 *
 * @property $key
 */
class DataResponse extends BaseObject
{
    protected $key;

    public function getKey()
    {
        return $this->key;
    }
}
