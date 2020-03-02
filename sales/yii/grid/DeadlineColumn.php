<?php

namespace sales\yii\grid;

use Yii;
use yii\grid\DataColumn;

class DeadlineColumn extends DataColumn
{
    public $timeAttribute;

    public function getDataCellValue($model, $key, $index): ?string
    {
        $attr = $this->timeAttribute ?: $this->attribute;

        if ($model->{$attr} === null) {
            return '';
        }

        $time = strtotime($model->{$attr});

        if (time() >= $time) {
            return 'expired';
        }

        $left = Yii::$app->formatter->asDuration($time - time());

        if (strpos($left, 'seconds') !== 0) {
            return substr($left, 0, strripos($left, ','));
        }

        return $left;
    }
}
