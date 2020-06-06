<?php

namespace common\models;

use common\models\query\ConferenceQuery;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "conference".
 *
 * @property int $cf_id
 * @property int $cf_cr_id
 * @property string $cf_sid
 * @property int $cf_status_id
 * @property string $cf_options
 * @property string $cf_recording_url
 * @property int $cf_recording_duration
 * @property string $cf_recording_sid
 * @property string $cf_created_dt
 * @property string $cf_updated_dt
 * @property string $cf_friendly_name
 * @property string $cf_call_sid
 * @property int|null $cf_created_user_id
 *
 * @property ConferenceRoom $cfCr
 * @property ConferenceParticipant[] $conferenceParticipants
 */
class Conference extends \yii\db\ActiveRecord
{
    public const EVENT_CONFERENCE_END = 'conference-end';
    public const EVENT_CONFERENCE_START = 'conference-start';
    public const EVENT_PARTICIPANT_LEAVE = 'participant-leave';
    public const EVENT_PARTICIPANT_JOIN = 'participant-join';
    public const EVENT_PARTICIPANT_MUTE = 'participant-mute';
    public const EVENT_PARTICIPANT_UNMUTE = 'participant-unmute';
    public const EVENT_PARTICIPANT_HOLD = 'participant-hold';
    public const EVENT_PARTICIPANT_UNHOLD = 'participant-unhold';
    public const EVENT_PARTICIPANT_SPEECH_START = 'participant-speech-start';
    public const EVENT_PARTICIPANT_SPEECH_STOP = 'participant-speech-stop';

    public const STATUS_START   = 1;
    public const STATUS_DELAY   = 2;
    public const STATUS_END     = 3;

    public const STATUS_LIST     = [
        self::STATUS_START  => 'Start',
        self::STATUS_DELAY  => 'Delay',
        self::STATUS_END    => 'End',
    ];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'conference';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
//            [['cf_cr_id'], 'required'],
            [['cf_cr_id', 'cf_status_id', 'cf_recording_duration'], 'integer'],
            [['cf_options'], 'string'],
            [['cf_created_dt', 'cf_updated_dt'], 'safe'],
            [['cf_sid', 'cf_recording_sid'], 'string', 'max' => 34],
            [['cf_sid', 'cf_recording_sid'], 'unique'],
            [['cf_recording_url'], 'string', 'max' => 200],
            [['cf_cr_id'], 'exist', 'skipOnError' => true, 'targetClass' => ConferenceRoom::class, 'targetAttribute' => ['cf_cr_id' => 'cr_id']],

            ['cf_created_user_id', 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['cf_created_user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'cf_id' => 'ID',
            'cf_cr_id' => 'Conference Room',
            'cf_sid' => 'Conference SID',
            'cf_status_id' => 'Status',
            'cf_options' => 'Options',
            'cf_created_dt' => 'Created Dt',
            'cf_updated_dt' => 'Updated Dt',
            'cf_friendly_name' => 'Friendly name',
            'cf_created_user_id' => 'Created User',
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
                    ActiveRecord::EVENT_BEFORE_INSERT => ['cf_created_dt', 'cf_updated_dt'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['cf_updated_dt'],
                ],
                'value' => date('Y-m-d H:i:s') //new Expression('NOW()'),
            ],
        ];
    }

    public function getCreatedUser(): ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 'cf_created_user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCfCr()
    {
        return $this->hasOne(ConferenceRoom::class, ['cr_id' => 'cf_cr_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getConferenceParticipants()
    {
        return $this->hasMany(ConferenceParticipant::class, ['cp_cf_id' => 'cf_id']);
    }

    /**
     * {@inheritdoc}
     * @return ConferenceQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new ConferenceQuery(static::class);
    }

    /**
     * @return string
     */
    public function getStatusName(): string
    {
        return self::STATUS_LIST[$this->cf_status_id] ?? '';
    }

    /**
     * @return array
     */
    public static function getList(): array
    {
        return self::STATUS_LIST;
    }

    public function start(): void
    {
        $this->cf_status_id = self::STATUS_START;
    }

    public function delay(): void
    {
        $this->cf_status_id = self::STATUS_DELAY;
    }

    public function end(): void
    {
        $this->cf_status_id = self::STATUS_END;
    }
}
