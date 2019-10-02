<?php

namespace common\models;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "qcall_config".
 *
 * @property int $qc_status_id
 * @property int $qc_call_att
 * @property int $qc_client_time_enable
 * @property int $qc_time_from
 * @property int $qc_time_to
 * @property string $qc_created_dt
 * @property string $qc_updated_dt
 * @property int $qc_created_user_id
 * @property int $qc_updated_user_id
 *
 * @property Employee $qcCreatedUser
 * @property Employee $qcUpdatedUser
 */
class QcallConfig extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'qcall_config';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['qc_status_id', 'qc_call_att', 'qc_time_from', 'qc_time_to'], 'required'],
            [['qc_status_id', 'qc_call_att', 'qc_client_time_enable', 'qc_time_from', 'qc_time_to', 'qc_created_user_id', 'qc_updated_user_id'], 'integer'],
            [['qc_created_dt', 'qc_updated_dt'], 'safe'],
            [['qc_status_id', 'qc_call_att'], 'unique', 'targetAttribute' => ['qc_status_id', 'qc_call_att']],
            [['qc_created_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['qc_created_user_id' => 'id']],
            [['qc_updated_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['qc_updated_user_id' => 'id']],
        ];
    }

    /**
     * @return array
     */
    public function behaviors(): array
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['qc_created_dt', 'qc_updated_dt'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['qc_updated_dt'],
                ],
                'value' => date('Y-m-d H:i:s') //new Expression('NOW()'),
            ],
            'user' => [
                'class' => BlameableBehavior::class,
                'createdByAttribute' => 'qc_created_user_id',
                'updatedByAttribute' => 'qc_updated_user_id',
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'qc_status_id' => 'Status ID',
            'qc_call_att' => 'Call Att',
            'qc_client_time_enable' => 'Client Time Enable',
            'qc_time_from' => 'Time From',
            'qc_time_to' => 'Time To',
            'qc_created_dt' => 'Created Dt',
            'qc_updated_dt' => 'Updated Dt',
            'qc_created_user_id' => 'Created User ID',
            'qc_updated_user_id' => 'Updated User ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getQcCreatedUser()
    {
        return $this->hasOne(Employee::class, ['id' => 'qc_created_user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getQcUpdatedUser()
    {
        return $this->hasOne(Employee::class, ['id' => 'qc_updated_user_id']);
    }

    /**
     * {@inheritdoc}
     * @return QcallConfigQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new QcallConfigQuery(get_called_class());
    }
}
