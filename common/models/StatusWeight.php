<?php

namespace common\models;

use common\models\query\StatusWeightQuery;

/**
 * This is the model class for table "{{%status_weight}}".
 *
 * @property int $sw_status_id
 * @property int|null $sw_weight
 */
class StatusWeight extends \yii\db\ActiveRecord
{
    public function rules(): array
    {
        return [
            ['sw_status_id', 'required'],
            ['sw_status_id', 'integer'],
            ['sw_status_id', 'filter', 'filter' => 'intval'],
            ['sw_status_id', 'in', 'range' => array_keys(Lead::STATUS_LIST)],
            ['sw_status_id', 'unique', 'message' => 'This status has already been taken.'],

            ['sw_weight', 'required'],
            ['sw_weight', 'integer'],
            ['sw_weight', 'filter', 'filter' => 'intval'],
        ];
    }

    public function getStatusName()
    {
        return Lead::STATUS_LIST[$this->sw_status_id] ?? 'Undefined';
    }

    public function attributeLabels(): array
    {
        return [
            'statusName' => 'Status',
            'sw_status_id' => 'Status',
            'sw_weight' => 'Weight',
        ];
    }

    public static function find(): StatusWeightQuery
    {
        return new StatusWeightQuery(get_called_class());
    }

    public static function tableName(): string
    {
        return '{{%status_weight}}';
    }
}
