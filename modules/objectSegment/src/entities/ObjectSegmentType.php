<?php

namespace modules\objectSegment\src\entities;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 *  * This is the model class for table "object_segment_type".
 *
 * @property int $ost_id
 * @property string $ost_key
 * @property string $ost_created_dt
 *
 */
class ObjectSegmentType extends \yii\db\ActiveRecord
{
    public static function tableName(): string
    {
        return 'object_segment_types';
    }

    public function rules(): array
    {
        return [
            ['ost_key', 'unique', 'targetAttribute' => 'ost_key'],
            ['ost_created_dt', 'datetime', 'format' => 'php:Y-m-d H:i:s'],
        ];
    }

    public function behaviors(): array
    {
        $behaviors = [
            'timestamp' => [
                'class'      => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['ost_created_dt'],
                ],
                'value'      => date('Y-m-d H:i:s')
            ],
        ];
        return ArrayHelper::merge(parent::behaviors(), $behaviors);
    }

    public function attributeLabels(): array
    {
        return [
            'ost_id' => 'ID',
            'ost_key' => 'Key',
            'ost_created_dt' => 'Created Dt',
        ];
    }
}
