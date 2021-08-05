<?php

namespace common\components\grid;

use Yii;
use yii\grid\DataColumn;
use yii\helpers\VarDumper;

/**
 * Class ApiUserColumn
 *
 * Ex.
    [
        'class' => \common\components\grid\ApiUserColumn::class,
        'attribute' => 'fr_created_api_user_id',
    ],
 *
 */
class ApiUserColumn extends DataColumn
{
    /**
     * @param mixed $model
     * @param mixed $key
     * @param int $index
     * @return string|null
     */
    public function getDataCellValue($model, $key, $index): ?string
    {
        if ($model->{$this->attribute} && $model->getApiUsername()) {
            return $model->getApiUsername() . ' (' . $model->fr_created_api_user_id . ')';
        }

        return null;
    }
}
