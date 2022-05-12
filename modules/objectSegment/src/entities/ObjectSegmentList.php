<?php

namespace modules\objectSegment\src\entities;

use common\models\Employee;
use Yii;
use yii\behaviors\AttributeBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 *  * This is the model class for table "object_segment_list".
 *
 * @property int $osl_id
 * @property int $osl_ost_id
 * @property string $osl_key
 * @property string $osl_title
 * @property string $osl_description
 * @property bool $osl_enabled
 * @property int $osl_updated_user_id
 * @property string $osl_updated_dt
 * @property string $osl_created_dt
 * @property ObjectSegmentType $oslObjectSegmentType
 * @property Employee $oslUpdatedUser
 *
 */
class ObjectSegmentList extends \yii\db\ActiveRecord
{
    public static function tableName(): string
    {
        return 'object_segment_list';
    }

    public function rules(): array
    {
        return [
            [['osl_ost_id', 'osl_key', 'osl_title'], 'required'],
            [
                'osl_ost_id',
                'exist',
                'skipOnError'     => true,
                'targetClass'     => ObjectSegmentType::class,
                'targetAttribute' => ['osl_ost_id' => 'ost_id']
            ],
            [['osl_key', 'osl_ost_id'], 'unique', 'targetAttribute' => ['osl_key', 'osl_ost_id']],
            [['osl_key', 'osl_title'], 'string', 'max' => 100],
            [['osl_description'], 'string', 'max' => 1000],
            ['osl_enabled', 'boolean'],
        ];
    }

    public function getOslObjectSegmentType()
    {
        return $this->hasOne(ObjectSegmentType::class, ['ost_id' => 'osl_ost_id']);
    }

    public function getOslUpdatedUser()
    {
        return $this->hasOne(Employee::class, ['id' => 'osl_updated_user_id']);
    }


    public function behaviors(): array
    {
        $behaviors = [
            'timestamp' => [
                'class'      => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['osl_created_dt', 'osl_updated_dt'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['osl_updated_dt'],
                ],
                'value'      => date('Y-m-d H:i:s')
            ],
            'attribute' => [
                'class'      => AttributeBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['osl_updated_user_id'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['osl_updated_user_id'],
                ],
                'value'      => isset(Yii::$app->user) ? Yii::$app->user->id : null,
            ],
        ];
        return ArrayHelper::merge(parent::behaviors(), $behaviors);
    }

    public function attributeLabels(): array
    {
        return [
            'osl_id'              => 'ID',
            'osl_ost_id'          => 'Object Segment Type ID',
            'osl_key'             => 'Key',
            'osl_title'           => 'Title',
            'osl_description'     => 'Description',
            'osl_enabled'         => 'Enabled',
            'osl_updated_user_id' => 'Updated User ID',
            'osl_created_dt'      => 'Created Dt',
            'osl_updated_dt'      => 'Updated Dt',
        ];
    }
}
