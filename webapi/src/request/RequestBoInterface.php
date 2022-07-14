<?php

namespace webapi\src\request;

use yii\base\Model;

interface RequestBoInterface
{
    public function prepareAdditionalInfo(Model $model): array;
}
