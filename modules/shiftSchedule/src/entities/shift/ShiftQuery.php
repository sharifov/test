<?php

namespace modules\shiftSchedule\src\entities\shift;

use yii\helpers\ArrayHelper;

class ShiftQuery
{
    public static function getList(): array
    {
        $data = Shift::find()->select(['sh_id', 'sh_name'])->enabled()->asArray()->all();
        return ArrayHelper::map($data, 'sh_id', 'sh_name');
    }
}
