<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "call_user_access".
 *
 * @property int $cua_call_id
 * @property int $cua_user_id
 * @property int $cua_status_id
 * @property string $cua_created_dt
 * @property string $cua_updated_dt
 *
 * @property Call $cuaCall
 * @property Employee $cuaUser
 */
class CallUserAccess extends \yii\db\ActiveRecord
{

    public const STATUS_TYPE_PENDING = 1;
    public const STATUS_TYPE_ACCEPT = 2;
    public const STATUS_TYPE_SKIP = 3;
    public const STATUS_TYPE_BUSY = 4;
    public const STATUS_TYPE_NO_ANSWERED = 5;

    public const STATUS_TYPE_LIST = [
        self::STATUS_TYPE_PENDING       => 'Pending',
        self::STATUS_TYPE_ACCEPT        => 'Accept',
        self::STATUS_TYPE_SKIP          => 'Skip',
        self::STATUS_TYPE_BUSY          => 'Busy',
        self::STATUS_TYPE_NO_ANSWERED   => 'No Answered',
    ];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'call_user_access';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['cua_call_id', 'cua_user_id'], 'required'],
            [['cua_call_id', 'cua_user_id', 'cua_status_id'], 'integer'],
            [['cua_created_dt', 'cua_updated_dt'], 'safe'],
            [['cua_call_id', 'cua_user_id'], 'unique', 'targetAttribute' => ['cua_call_id', 'cua_user_id']],
            [['cua_call_id'], 'exist', 'skipOnError' => true, 'targetClass' => Call::class, 'targetAttribute' => ['cua_call_id' => 'c_id']],
            [['cua_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['cua_user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'cua_call_id' => 'Call ID',
            'cua_user_id' => 'User ID',
            'cua_status_id' => 'Status ID',
            'cua_created_dt' => 'Created Dt',
            'cua_updated_dt' => 'Updated Dt',
        ];
    }

    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['cua_created_dt', 'cua_updated_dt'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['cua_updated_dt'],
                ],
                'value' => date('Y-m-d H:i:s') //new Expression('NOW()'),
            ],
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCuaCall()
    {
        return $this->hasOne(Call::class, ['c_id' => 'cua_call_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCuaUser()
    {
        return $this->hasOne(Employee::class, ['id' => 'cua_user_id']);
    }

    /**
     * {@inheritdoc}
     * @return CallUserAccessQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new CallUserAccessQuery(get_called_class());
    }

    /**
     * @return array
     */
    public static function getStatusTypeList(): array
    {
        return self::STATUS_TYPE_LIST;
    }

    /**
     * @return mixed|string
     */
    public function getStatusTypeName()
    {
        return self::STATUS_TYPE_LIST[$this->cua_status_id] ?? '-';
    }

    public function acceptPending(): void
    {
        $this->cua_status_id = self::STATUS_TYPE_PENDING;
    }

    public function acceptCall(): void
    {
        $this->cua_status_id = self::STATUS_TYPE_ACCEPT;
    }

    public function skipCall(): void
    {
        $this->cua_status_id = self::STATUS_TYPE_SKIP;
    }

    public function busyCall(): void
    {
        $this->cua_status_id = self::STATUS_TYPE_BUSY;
    }

    public function noAnsweredCall(): void
    {
        $this->cua_status_id = self::STATUS_TYPE_NO_ANSWERED;
    }

    /**
     * @param bool $insert
     * @param array $changedAttributes
     */
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        if ($insert) {
            Notifications::create($this->cua_user_id, 'New incoming Call (' . $this->cua_call_id . ')', 'New incoming Call (' . $this->cua_call_id . ')', Notifications::TYPE_SUCCESS, true);
            Notifications::socket($this->cua_user_id, null, 'getNewNotification', [], true);
        }

        if(isset($changedAttributes['cua_status_id'])) {
            Notifications::socket($this->cua_user_id, null, 'updateIncomingCall', $this->attributes);
        }
    }
}
