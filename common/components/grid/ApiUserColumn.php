<?php

namespace common\components\grid;

use common\models\ApiUser;
use Yii;
use yii\grid\DataColumn;

/**
 * Class ApiUserColumn
 *
 * Ex.
    [
        'class' => \common\components\grid\ApiUserColumn::class,
        'attribute' => 'fr_created_api_user_id',
        'relation' => 'apiUserName',
    ],
 *
 */
class ApiUserColumn extends DataColumn
{
    public $relation;

    public function init(): void
    {
        parent::init();

        if (empty($this->relation)) {
            throw new \InvalidArgumentException('relation must be set.');
        }

        $this->format = 'raw';
    }

    /**
     * @param mixed $model
     * @param mixed $key
     * @param int $index
     * @return string|null
     */
    public function getDataCellValue($model, $key, $index)
    {
        if ($model->{$this->attribute} && ($apiUser = $model->{$this->relation})) {
            /** @var ApiUser $apiUser */
            return $apiUser->au_name . ' (' . $model->fr_created_api_user_id . ')';
        }

        return Yii::$app->formatter->nullDisplay;
    }
}
