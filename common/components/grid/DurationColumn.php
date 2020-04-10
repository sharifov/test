<?php

namespace common\components\grid;

use Yii;
use yii\grid\DataColumn;

/**
 * Class DurationColumn
 *
 * @property string|null $startAttribute
 *
 * Ex.
        [
            'class' => \common\components\grid\DurationColumn::class,
            'attribute' => 'duration',
            'startAttribute' => 'start_dt',
        ],
 */
class DurationColumn extends DataColumn
{
    public $startAttribute;

    public function init(): void
    {
        parent::init();

        if ($this->startAttribute === null) {
            throw new \InvalidArgumentException('startAttribute cannot be null');
        }
    }

    public function getDataCellValue($model, $key, $index): string
    {
        return $model->{$this->attribute} > -1
            ? Yii::$app->formatter->asDuration($model->{$this->attribute})
            : Yii::$app->formatter->asDuration(time() - strtotime($model->{$this->startAttribute}));
    }
}
