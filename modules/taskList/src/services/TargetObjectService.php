<?php

namespace modules\taskList\src\services;

use common\models\Lead;
use modules\taskList\src\entities\TargetObject;
use src\helpers\DateHelper;
use yii\base\Model;

class TargetObjectService
{
    public static function getStatDataByTargetObject(string $targetObject, Model $model): \DateTimeImmutable
    {
        switch ($targetObject) {
            case TargetObject::TARGET_OBJ_LEAD:
                /* @var Lead $model */
                return DateHelper::getDateTimeImmutableUTC($model->created);

            /* TODO: Case  */
        }
        throw new \RuntimeException('targetObject (' . $targetObject . ') unprocessed');
    }
}
