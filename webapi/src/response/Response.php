<?php

namespace webapi\src\response;

use yii\base\BaseObject;

abstract class Response extends BaseObject
{
    abstract public function getResponse();
    abstract public function addData(string $key, array $data);
    abstract public function addDataResponse(DataResponse $response);
}
