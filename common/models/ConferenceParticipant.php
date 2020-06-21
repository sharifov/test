<?php

namespace common\models;

use common\models\query\ConferenceParticipantQuery;
use Yii;

/**
 * This is the model class for table "conference_participant".
 *
 * @property int $cp_id
 * @property int $cp_cf_id
 * @property string $cp_call_sid
 * @property int $cp_call_id
 * @property int $cp_status_id
 * @property string $cp_join_dt
 * @property string $cp_leave_dt
 * @property int|null $cp_type_id
 * @property string $cp_hold_dt
 *
 * @property Call $cpCall
 * @property Conference $cpCf
 */
class ConferenceParticipant extends \yii\db\ActiveRecord
{

    public const STATUS_JOIN    = 1;
    public const STATUS_LEAVE   = 2;
    public const STATUS_HOLD   = 3;
    public const STATUS_UNHOLD   = 4;
    public const STATUS_MUTE   = 5;
    public const STATUS_UNMUTE   = 6;

    public const STATUS_LIST = [
        self::STATUS_JOIN   => 'Join',
        self::STATUS_LEAVE   => 'Leave',
        self::STATUS_HOLD   => 'Hold',
        self::STATUS_UNHOLD   => 'UnHold',
        self::STATUS_MUTE   => 'Mute',
        self::STATUS_UNMUTE   => 'Unmute',
    ];

    public const TYPE_AGENT = 1;
    public const TYPE_CLIENT = 2;
    public const TYPE_USER = 3;

    public const TYPE_LIST = [
        self::TYPE_AGENT => 'Agent',
        self::TYPE_CLIENT => 'Client',
        self::TYPE_USER => 'User',
    ];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'conference_participant';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['cp_status_id', 'integer'],
            ['cp_status_id', 'in', 'range' => array_keys(self::STATUS_LIST)],

            [['cp_cf_id'], 'required'],
            [['cp_cf_id', 'cp_call_id'], 'integer'],
            [['cp_join_dt', 'cp_leave_dt'], 'safe'],
            [['cp_call_sid'], 'string', 'max' => 34],
//            [['cp_call_sid'], 'unique'],
            [['cp_call_id'], 'exist', 'skipOnError' => true, 'targetClass' => Call::class, 'targetAttribute' => ['cp_call_id' => 'c_id']],
            [['cp_cf_id'], 'exist', 'skipOnError' => true, 'targetClass' => Conference::class, 'targetAttribute' => ['cp_cf_id' => 'cf_id']],

            ['cp_type_id', 'integer'],
            ['cp_type_id', 'in', 'range' => array_keys(self::TYPE_LIST)],

            ['cp_hold_dt', 'datetime', 'format' => 'php:Y-m-d H:i:s'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'cp_id' => 'ID',
            'cp_cf_id' => 'Conference ID',
            'cp_call_sid' => 'Call Sid',
            'cp_call_id' => 'Call ID',
            'cp_status_id' => 'Status',
            'cp_join_dt' => 'Join Dt',
            'cp_leave_dt' => 'Leave Dt',
            'cp_type_id' => 'Type',
            'cp_hold_dt' => 'Hold Dt',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCpCall()
    {
        return $this->hasOne(Call::class, ['c_id' => 'cp_call_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCpCf()
    {
        return $this->hasOne(Conference::class, ['cf_id' => 'cp_cf_id']);
    }

    /**
     * {@inheritdoc}
     * @return ConferenceParticipantQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new ConferenceParticipantQuery(static::class);
    }

    /**
     * @return string
     */
    public function getStatusName(): string
    {
        return self::STATUS_LIST[$this->cp_status_id] ?? '';
    }

    /**
     * @return array
     */
    public static function getStatusList(): array
    {
        return self::STATUS_LIST;
    }

    public static function getTypeName(?int $value)
    {
        return self::TYPE_LIST[$value] ?? 'Undefined';
    }

    public static function getTypeList(): array
    {
        return self::TYPE_LIST;
    }

    public function join(): void
    {
        $this->cp_status_id = self::STATUS_JOIN;
    }

    public function isJoin(): bool
    {
        return $this->cp_status_id === self::STATUS_JOIN;
    }

    public function hold(string $holdTime): void
    {
        $this->cp_status_id = self::STATUS_HOLD;
        $this->cp_hold_dt = $holdTime;
    }

    public function isHold(): bool
    {
        return $this->cp_status_id === self::STATUS_HOLD;
    }

    public function unhold(): void
    {
        $this->cp_status_id = self::STATUS_UNHOLD;
    }

    public function isUnhold(): bool
    {
        return $this->cp_status_id === self::STATUS_UNHOLD;
    }

    public function leave(): void
    {
        $this->cp_status_id = self::STATUS_LEAVE;
    }

    public function isLeave(): bool
    {
        return $this->cp_status_id === self::STATUS_LEAVE;
    }

    public function mute(): void
    {
        $this->cp_status_id = self::STATUS_MUTE;
    }

    public function isMute(): bool
    {
        return $this->cp_status_id === self::STATUS_MUTE;
    }

    public function unmute(): void
    {
        $this->cp_status_id = self::STATUS_UNMUTE;
    }

    public function isUnmute(): bool
    {
        return $this->cp_status_id === self::STATUS_UNMUTE;
    }

    public function isAgent(): bool
    {
        return $this->cp_type_id === self::TYPE_AGENT;
    }

    public function isClient(): bool
    {
        return $this->cp_type_id === self::TYPE_CLIENT;
    }

    public function isUser(): bool
    {
        return $this->cp_type_id === self::TYPE_USER;
    }
}
