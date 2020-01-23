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
 *
 * @property Call $cpCall
 * @property Conference $cpCf
 */
class ConferenceParticipant extends \yii\db\ActiveRecord
{

    public const STATUS_JOIN    = 1;
    public const STATUS_LEAVE   = 2;

    public const STATUS_LIST = [
        self::STATUS_JOIN   => 'Join',
        self::STATUS_LEAVE   => 'Leave',
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
            [['cp_cf_id'], 'required'],
            [['cp_cf_id', 'cp_call_id', 'cp_status_id'], 'integer'],
            [['cp_join_dt', 'cp_leave_dt'], 'safe'],
            [['cp_call_sid'], 'string', 'max' => 34],
            [['cp_call_sid'], 'unique'],
            [['cp_call_id'], 'exist', 'skipOnError' => true, 'targetClass' => Call::class, 'targetAttribute' => ['cp_call_id' => 'c_id']],
            [['cp_cf_id'], 'exist', 'skipOnError' => true, 'targetClass' => Conference::class, 'targetAttribute' => ['cp_cf_id' => 'cf_id']],
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
            'cp_status_id' => 'Status ID',
            'cp_join_dt' => 'Join Dt',
            'cp_leave_dt' => 'Leave Dt',
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
    public static function getList(): array
    {
        return self::STATUS_LIST;
    }
}
