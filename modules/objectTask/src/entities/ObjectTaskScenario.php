<?php

namespace modules\objectTask\src\entities;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\BaseActiveRecord;
use yii\helpers\Json;

/**
 * This is the model class for table "object_task_scenario".
 *
 * @property int $ots_id
 * @property string $ots_key
 * @property string|null $ots_data_json
 * @property string|null $ots_updated_dt
 * @property int|null $ots_updated_user_id
 * @property int|null $ots_enable
 *
 * @property ObjectTask[] $objectTasks
 */
class ObjectTaskScenario extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'object_task_scenario';
    }

    public function behaviors(): array
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    BaseActiveRecord::EVENT_BEFORE_UPDATE => ['ots_updated_dt']
                ],
                'value' => date('Y-m-d H:i:s'),
            ],
            'user' => [
                'class' => BlameableBehavior::class,
                'attributes' => [
                    BaseActiveRecord::EVENT_BEFORE_UPDATE => ['ots_updated_user_id']
                ],
                'defaultValue' => null,
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ots_key', 'ots_enable'], 'required'],
            [['ots_data_json', 'ots_updated_dt'], 'safe'],
            [['ots_updated_user_id', 'ots_enable'], 'integer'],
            [['ots_key'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'ots_id' => 'ID',
            'ots_key' => 'Key',
            'ots_data_json' => 'Data Json',
            'ots_updated_dt' => 'Updated Dt',
            'ots_updated_user_id' => 'Updated User ID',
            'ots_enable' => 'Enable',
        ];
    }

    public function beforeSave($insert): bool
    {
        if (!parent::beforeSave($insert)) {
            return false;
        }

        if ($this->ots_data_json && is_string($this->ots_data_json)) {
            $this->ots_data_json = Json::decode($this->ots_data_json);
        }

        return true;
    }

    /**
     * Gets query for [[ObjectTasks]].
     *
     * @return \yii\db\ActiveQuery|ObjectTaskScopes
     */
    public function getObjectTasks()
    {
        return $this->hasMany(ObjectTask::class, ['ot_ots_id' => 'ots_id']);
    }

    /**
     * {@inheritdoc}
     * @return ObjectTaskScenarioScopes the active query used by this AR class.
     */
    public static function find()
    {
        return new ObjectTaskScenarioScopes(get_called_class());
    }
}
