<?php

namespace common\models;

use common\components\jobs\AgentCallQueueJob;
use Yii;

/**
 * This is the model class for table "user_call_status".
 *
 * @property int $us_id
 * @property int $us_type_id
 * @property int $us_user_id
 * @property string $us_created_dt
 *
 * @property Employee $usUser
 */
class UserCallStatus extends \yii\db\ActiveRecord
{

    public const STATUS_TYPE_READY = 1;
    public const STATUS_TYPE_OCCUPIED = 2;

    public const STATUS_TYPE_LIST = [
        self::STATUS_TYPE_READY => 'Is Ready',
        self::STATUS_TYPE_OCCUPIED => 'Is Occupied',
    ];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user_call_status';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['us_type_id', 'us_user_id'], 'integer'],
            [['us_created_dt'], 'safe'],
            [['us_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['us_user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'us_id' => 'ID',
            'us_type_id' => 'Type',
            'us_user_id' => 'User',
            'us_created_dt' => 'Created Dt',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUsUser()
    {
        return $this->hasOne(Employee::class, ['id' => 'us_user_id']);
    }

    /**
     * {@inheritdoc}
     * @return UserCallStatusQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new UserCallStatusQuery(get_called_class());
    }

    /**
     * @return mixed|string
     */
    public function getStatusTypeName()
    {
        return self::STATUS_TYPE_LIST[$this->us_type_id] ?? '-';
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        if( $this->us_type_id === self::STATUS_TYPE_READY) {
            //Call::applyHoldCallToAgent($this->us_user_id);
            $job = new AgentCallQueueJob();
            $job->user_id = $this->us_user_id;
            $jobId = Yii::$app->queue_job->delay(5)->priority(150)->push($job);
        }
    }


}
