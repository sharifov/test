<?php

namespace common\models;

use borales\extensions\phoneInput\PhoneInputValidator;
use common\models\query\ConferenceRoomQuery;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "conference_room".
 *
 * @property int $cr_id
 * @property string $cr_key
 * @property string $cr_name
 * @property string $cr_phone_number
 * @property bool $cr_enabled
 * @property string $cr_start_dt
 * @property string $cr_end_dt
 * @property bool $cr_param_muted
 * @property string $cr_param_beep
 * @property bool $cr_param_start_conference_on_enter
 * @property bool $cr_param_end_conference_on_exit
 * @property int $cr_param_max_participants
 * @property string $cr_param_record
 * @property string $cr_param_region
 * @property string $cr_param_trim
 * @property string $cr_param_wait_url
 * @property string $cr_moderator_phone_number
 * @property string $cr_welcome_message
 * @property string $cr_created_dt
 * @property string $cr_updated_dt
 * @property int $cr_created_user_id
 * @property int $cr_updated_user_id
 *
 * @property Conference[] $conferences
 * @property Employee $crCreatedUser
 * @property Employee $crUpdatedUser
 */
class ConferenceRoom extends \yii\db\ActiveRecord
{
    public const PARAM_BEEP_TRUE        = 'true';
    public const PARAM_BEEP_FALSE       = 'false';
    public const PARAM_BEEP_ON_ENTER    = 'onEnter';
    public const PARAM_BEEP_ON_EXIT     = 'onExit';

    public const PARAM_BEEP_LIST    = [
        self::PARAM_BEEP_TRUE           => 'true',
        self::PARAM_BEEP_FALSE          => 'false',
        self::PARAM_BEEP_ON_ENTER       => 'on Enter',
        self::PARAM_BEEP_ON_EXIT        => 'on Exit',
    ];

    public const PARAM_REGION_US1 = 'us1';
    public const PARAM_REGION_JE1 = 'ie1';
    public const PARAM_REGION_DE1 = 'de1';
    public const PARAM_REGION_SG1 = 'sg1';
    public const PARAM_REGION_BR1 = 'br1';
    public const PARAM_REGION_AU1 = 'au1';
    public const PARAM_REGION_JP1 = 'jp1';

    public const PARAM_REGION_LIST = [
        self::PARAM_REGION_US1 => 'US',
        self::PARAM_REGION_JE1 => 'JE',
        self::PARAM_REGION_DE1 => 'DE',
        self::PARAM_REGION_SG1 => 'SG',
        self::PARAM_REGION_BR1 => 'BR',
        self::PARAM_REGION_AU1 => 'AU',
        self::PARAM_REGION_JP1 => 'JP',
    ];


    public const PARAM_TRIM_SILENCE     = 'trim-silence';
    public const PARAM_TRIM_NOT_TRIM    = 'do-not-trim';

    public const PARAM_TRIM_LIST    = [
        self::PARAM_TRIM_SILENCE    => 'Trim silence',
        self::PARAM_TRIM_NOT_TRIM   => 'Do not trim',
    ];


    public const PARAM_RECORD_NOT_RECORD    = 'do-not-record';
    public const PARAM_RECORD_FROM_START    = 'record-from-start';

    public const PARAM_RECORD_LIST    = [
        self::PARAM_RECORD_NOT_RECORD    => 'do not record',
        self::PARAM_RECORD_FROM_START   => 'record from start',
    ];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'conference_room';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['cr_key', 'cr_name', 'cr_phone_number'], 'required'],

            [['cr_key', 'cr_name'], 'match', 'pattern' => "/^[0-9a-z-\s\']+$/i"],

            [['cr_param_max_participants', 'cr_created_user_id', 'cr_updated_user_id'], 'integer'],
            [['cr_start_dt', 'cr_end_dt', 'cr_created_dt', 'cr_updated_dt'], 'safe'],
            [['cr_welcome_message'], 'string'],

            [['cr_enabled', 'cr_param_muted', 'cr_param_start_conference_on_enter', 'cr_param_end_conference_on_exit'], 'boolean'],

            [['cr_key'], 'string', 'max' => 30],
            [['cr_name'], 'string', 'max' => 50],
            [['cr_phone_number', 'cr_moderator_phone_number'], 'string', 'max' => 18],
            [['cr_param_beep'], 'string', 'max' => 10],
            [['cr_param_record'], 'string', 'max' => 20],
            [['cr_param_region'], 'string', 'max' => 3],
            [['cr_param_trim'], 'string', 'max' => 15],
            [['cr_param_wait_url'], 'string', 'max' => 255],
            [['cr_key'], 'unique'],
            [['cr_phone_number', 'cr_moderator_phone_number'], PhoneInputValidator::class],
            [['cr_created_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['cr_created_user_id' => 'id']],
            [['cr_updated_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['cr_updated_user_id' => 'id']],
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
                    ActiveRecord::EVENT_BEFORE_INSERT => ['cr_created_dt', 'cr_updated_dt'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['cr_updated_dt'],
                ],
                'value' => date('Y-m-d H:i:s') //new Expression('NOW()'),
            ],
            'user' => [
                'class' => BlameableBehavior::class,
                'createdByAttribute' => 'cr_created_user_id',
                'updatedByAttribute' => 'cr_updated_user_id',
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'cr_id' => 'ID',
            'cr_key' => 'Key',
            'cr_name' => 'Name',
            'cr_phone_number' => 'Phone Number',
            'cr_enabled' => 'Enabled',
            'cr_start_dt' => 'Start DateTime',
            'cr_end_dt' => 'End DateTime',
            'cr_param_muted' => 'Muted',
            'cr_param_beep' => 'Beep',
            'cr_param_start_conference_on_enter' => 'Start Conference On Enter',
            'cr_param_end_conference_on_exit' => 'End Conference On Exit',
            'cr_param_max_participants' => 'Max Participants',
            'cr_param_record' => 'Record',
            'cr_param_region' => 'Region',
            'cr_param_trim' => 'Trim',
            'cr_param_wait_url' => 'Wait Url',
            'cr_moderator_phone_number' => 'Moderator Phone Number',
            'cr_welcome_message' => 'Welcome Message',
            'cr_created_dt' => 'Created DateTime',
            'cr_updated_dt' => 'Updated DateTime',
            'cr_created_user_id' => 'Created User',
            'cr_updated_user_id' => 'Updated User',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getConferences()
    {
        return $this->hasMany(Conference::class, ['cf_cr_id' => 'cr_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCrCreatedUser()
    {
        return $this->hasOne(Employee::class, ['id' => 'cr_created_user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCrUpdatedUser()
    {
        return $this->hasOne(Employee::class, ['id' => 'cr_updated_user_id']);
    }

    /**
     * {@inheritdoc}
     * @return ConferenceRoomQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new ConferenceRoomQuery(static::class);
    }

    /**
     * @return array
     */
    public static function getParamRecordList(): array
    {
        return self::PARAM_RECORD_LIST;
    }

    /**
     * @return array
     */
    public static function getParamTrimList(): array
    {
        return self::PARAM_TRIM_LIST;
    }

    /**
     * @return array
     */
    public static function getParamRegionList(): array
    {
        return self::PARAM_REGION_LIST;
    }

    /**
     * @return array
     */
    public static function getParamBeepList(): array
    {
        return self::PARAM_BEEP_LIST;
    }

    /**
     * @param bool $enabled
     * @return array
     */
    public static function getList(bool $enabled = true): array
    {
        $query = self::find()->orderBy(['cr_id' => SORT_ASC]);
        if ($enabled) {
            $query->andWhere(['cr_enabled' => true]);
        }
        $data = $query->asArray()->all();
        return ArrayHelper::map($data, 'cr_id', 'cr_name');
    }

    /**
     * @return array
     */
    public function getCreatedTwParams(): array
    {
        if ($this->cr_param_muted) {
            $params['muted'] = $this->cr_param_muted;
        }

        if ($this->cr_param_beep) {
            $params['beep'] = $this->cr_param_beep;     // true, false, onEnter, onExit
        }

        if ($this->cr_param_start_conference_on_enter) {
            $params['startConferenceOnEnter']       = $this->cr_param_start_conference_on_enter;
        }

        if ($this->cr_param_end_conference_on_exit) {
            $params['endConferenceOnExit'] = $this->cr_param_end_conference_on_exit;
        }

        if ($this->cr_param_wait_url) {
            $params['waitUrl'] = $this->cr_param_wait_url; //'http://twimlets.com/holdmusic?Bucket=com.twilio.music.classical';
        }

        if ($this->cr_param_record) {
            $params['record'] = $this->cr_param_record;  // do-not-record or record-from-start
        }

        if ($this->cr_param_region) {
            $params['region'] = $this->cr_param_region;   // us1, ie1, de1, sg1, br1, au1, jp1
        }

        if ($this->cr_param_trim) {
            $params['trim'] = $this->cr_param_trim;    // trim-silence or do-not-trim
        }

        $communicationHost = Yii::$app->comms->url;

        $params['maxParticipants']                  = $this->cr_param_max_participants ?: 250;
        $params['statusCallbackEvent']              = 'start end join leave mute hold'; //speaker
        $params['statusCallback']                   = $communicationHost . 'twilio/conference-status-callback';
        $params['statusCallbackMethod']             = 'POST';
        $params['recordingStatusCallback']          = $communicationHost . 'twilio/conference-recording-status-callback';
        $params['recordingStatusCallbackMethod']    = 'POST';
        $params['recordingStatusCallbackEvent']     = 'completed'; // in-progress, completed, absent
        //$params['eventCallbackUrl']                 = 'http://'.Yii::$app->comms->host.'/v1/twilio/conference-event-callback';

        return $params;
    }
}
