<?php

namespace common\models;

use common\components\jobs\AgentCallQueueJob;
use common\models\query\UserCallStatusQuery;
use src\dispatchers\NativeEventDispatcher;
use src\helpers\setting\SettingHelper;
use src\model\user\entity\userCallStatus\events\UserCallStatusEvents;
use src\model\user\entity\userGroup\events\UserGroupEvents;
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

    public static function occupied(int $userId, \DateTimeImmutable $createdDt): self
    {
        $status = new self();
        $status->us_type_id = self::STATUS_TYPE_OCCUPIED;
        $status->us_user_id = $userId;
        $status->us_created_dt = $createdDt->format('Y-m-d H:i:s');
        return $status;
    }

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
            ['us_type_id','integer'],
            ['us_type_id', 'filter', 'filter' => 'intval', 'skipOnEmpty' => true],
            ['us_type_id', 'in', 'range' => array_keys(self::STATUS_TYPE_LIST)],

            ['us_created_dt', 'safe'],

            ['us_user_id', 'integer'],
            ['us_user_id', 'filter', 'filter' => 'intval', 'skipOnEmpty' => true],
            ['us_user_id', 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['us_user_id' => 'id']],
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
        return new UserCallStatusQuery(static::class);
    }

    /**
     * @return mixed|string
     */
    public function getStatusTypeName()
    {
        return self::STATUS_TYPE_LIST[$this->us_type_id] ?? '-';
    }

    /**
     * @param bool $insert
     * @param array $changedAttributes
     */
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        if ((int) $this->us_type_id === self::STATUS_TYPE_READY) {
            if (SettingHelper::isEnableAgentCallQueueJobAfterChangeCallStatusReady()) {
                $delayJob = 5;
                $job = new AgentCallQueueJob();
                $job->user_id = $this->us_user_id;
                $job->delayJob = $delayJob;
                $jobId = Yii::$app->queue_job->delay($delayJob)->priority(150)->push($job);
            }
        }

        if ($insert) {
            NativeEventDispatcher::recordEvent(UserCallStatusEvents::class, UserCallStatusEvents::INSERT, [UserCallStatusEvents::class, 'updateUserStatus'], $this);
            NativeEventDispatcher::trigger(UserCallStatusEvents::class, UserCallStatusEvents::INSERT);
        } else {
            if (isset($changedAttributes['us_type_id'])) {
                NativeEventDispatcher::recordEvent(
                    UserCallStatusEvents::class,
                    UserCallStatusEvents::UPDATE,
                    [UserCallStatusEvents::class, 'updateUserStatus'],
                    $this
                );
                NativeEventDispatcher::trigger(UserCallStatusEvents::class, UserCallStatusEvents::UPDATE);
            }
        }
    }

    /**
     * @return bool
     */
    public function beforeDelete(): bool
    {
        if (!parent::beforeDelete()) {
            return false;
        }

        NativeEventDispatcher::recordEvent(UserCallStatusEvents::class, UserCallStatusEvents::DELETE, [UserCallStatusEvents::class, 'resetCallPhoneStatus'], $this);
        return true;
    }

    public function isReady(): bool
    {
        return $this->us_type_id === self::STATUS_TYPE_READY;
    }


    /**
     *
     */
    public function afterDelete(): void
    {
        parent::afterDelete();
        NativeEventDispatcher::trigger(UserCallStatusEvents::class, UserCallStatusEvents::DELETE);
    }
}
