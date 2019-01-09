<?php

namespace common\models;

use common\components\CommunicationService;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\VarDumper;

/**
 * This is the model class for table "sms".
 *
 * @property int $s_id
 * @property int $s_reply_id
 * @property int $s_lead_id
 * @property int $s_project_id
 * @property string $s_phone_from
 * @property string $s_phone_to
 * @property string $s_sms_text
 * @property string $s_sms_data
 * @property int $s_type_id
 * @property int $s_template_type_id
 * @property string $s_language_id
 * @property int $s_communication_id
 * @property int $s_is_deleted
 * @property int $s_is_new
 * @property int $s_delay
 * @property int $s_priority
 * @property int $s_status_id
 * @property string $s_status_done_dt
 * @property string $s_read_dt
 * @property string $s_error_message
 * @property string $s_tw_price
 * @property string $s_tw_sent_dt
 * @property string $s_tw_account_sid
 * @property string $s_tw_message_sid
 * @property int $s_tw_num_segments
 * @property string $s_tw_to_country
 * @property string $s_tw_to_state
 * @property string $s_tw_to_city
 * @property string $s_tw_to_zip
 * @property string $s_tw_from_country
 * @property string $s_tw_from_state
 * @property string $s_tw_from_city
 * @property string $s_tw_from_zip
 * @property int $s_created_user_id
 * @property int $s_updated_user_id
 * @property string $s_created_dt
 * @property string $s_updated_dt
 *
 * @property Employee $sCreatedUser
 * @property Language $sLanguage
 * @property Lead $sLead
 * @property Project $sProject
 * @property SmsTemplateType $sTemplateType
 * @property Employee $sUpdatedUser
 */
class Sms extends \yii\db\ActiveRecord
{

    public const TYPE_DRAFT     = 0;
    public const TYPE_OUTBOX    = 1;
    public const TYPE_INBOX     = 2;

    public const TYPE_LIST = [
        self::TYPE_DRAFT    => 'Draft',
        self::TYPE_OUTBOX   => 'Outbox',
        self::TYPE_INBOX    => 'Inbox',
    ];


    public $quotes = [];

    public const STATUS_NEW     = 1;
    public const STATUS_PENDING = 2;
    public const STATUS_PROCESS = 3;
    public const STATUS_CANCEL  = 4;
    public const STATUS_DONE    = 5;
    public const STATUS_ERROR   = 6;

    public const STATUS_LIST = [
        self::STATUS_NEW        => 'New',
        self::STATUS_PENDING    => 'Pending',
        self::STATUS_PROCESS    => 'Process',
        self::STATUS_CANCEL     => 'Cancel',
        self::STATUS_DONE       => 'Done',
        self::STATUS_ERROR      => 'Error',
    ];

    public const PRIORITY_LOW       = 1;
    public const PRIORITY_NORMAL    = 2;
    public const PRIORITY_HIGH      = 3;

    public const PRIORITY_LIST = [
        self::PRIORITY_LOW      => 'Low',
        self::PRIORITY_NORMAL   => 'Normal',
        self::PRIORITY_HIGH     => 'High',
    ];


    public const FILTER_TYPE_ALL        = 1;
    public const FILTER_TYPE_INBOX      = 2;
    public const FILTER_TYPE_OUTBOX     = 3;
    public const FILTER_TYPE_DRAFT      = 4;
    public const FILTER_TYPE_TRASH      = 5;


    public const FILTER_TYPE_LIST = [
        self::FILTER_TYPE_ALL       => 'ALL',
        self::FILTER_TYPE_INBOX     => 'INBOX',
        self::FILTER_TYPE_OUTBOX    => 'OUTBOX',
        self::FILTER_TYPE_DRAFT     => 'DRAFT',
        self::FILTER_TYPE_TRASH     => 'TRASH',
    ];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'sms';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['s_reply_id', 's_lead_id', 's_project_id', 's_type_id', 's_template_type_id', 's_communication_id', 's_is_deleted', 's_is_new', 's_delay', 's_priority', 's_status_id', 's_tw_num_segments', 's_created_user_id', 's_updated_user_id'], 'integer'],
            [['s_phone_from', 's_phone_to'], 'required'],
            [['s_sms_text', 's_sms_data'], 'string'],
            [['s_status_done_dt', 's_read_dt', 's_tw_sent_dt', 's_created_dt', 's_updated_dt'], 'safe'],
            [['s_tw_price'], 'number'],
            [['s_phone_from', 's_phone_to'], 'string', 'max' => 255],
            [['s_language_id', 's_tw_to_country', 's_tw_from_country'], 'string', 'max' => 5],
            [['s_error_message'], 'string', 'max' => 500],
            [['s_tw_account_sid', 's_tw_message_sid'], 'string', 'max' => 40],
            [['s_tw_to_state', 's_tw_to_city', 's_tw_from_state', 's_tw_from_city'], 'string', 'max' => 30],
            [['s_tw_to_zip', 's_tw_from_zip'], 'string', 'max' => 10],
            [['s_created_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['s_created_user_id' => 'id']],
            [['s_language_id'], 'exist', 'skipOnError' => true, 'targetClass' => Language::class, 'targetAttribute' => ['s_language_id' => 'language_id']],
            [['s_lead_id'], 'exist', 'skipOnError' => true, 'targetClass' => Lead::class, 'targetAttribute' => ['s_lead_id' => 'id']],
            [['s_project_id'], 'exist', 'skipOnError' => true, 'targetClass' => Project::class, 'targetAttribute' => ['s_project_id' => 'id']],
            [['s_template_type_id'], 'exist', 'skipOnError' => true, 'targetClass' => SmsTemplateType::class, 'targetAttribute' => ['s_template_type_id' => 'stp_id']],
            [['s_updated_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['s_updated_user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            's_id' => 'ID',
            's_reply_id' => 'Reply ID',
            's_lead_id' => 'Lead ID',
            's_project_id' => 'Project ID',
            's_phone_from' => 'Phone From',
            's_phone_to' => 'Phone To',
            's_sms_text' => 'Sms Text',
            's_sms_data' => 'Sms Data',
            's_type_id' => 'Type ID',
            's_template_type_id' => 'Template Type ID',
            's_language_id' => 'Language ID',
            's_communication_id' => 'Communication ID',
            's_is_deleted' => 'Is Deleted',
            's_is_new' => 'Is New',
            's_delay' => 'Delay',
            's_priority' => 'Priority',
            's_status_id' => 'Status ID',
            's_status_done_dt' => 'Status Done Dt',
            's_read_dt' => 'Read Dt',
            's_error_message' => 'Error Message',
            's_tw_price' => 'Price',
            's_tw_sent_dt' => 'Sent Dt',
            's_tw_account_sid' => 'Account Sid',
            's_tw_message_sid' => 'Message Sid',
            's_tw_num_segments' => 'Num Segments',
            's_tw_to_country' => 'To Country',
            's_tw_to_state' => 'To State',
            's_tw_to_city' => 'To City',
            's_tw_to_zip' => 'To Zip',
            's_tw_from_country' => 'From Country',
            's_tw_from_state' => 'From State',
            's_tw_from_city' => 'From City',
            's_tw_from_zip' => 'From Zip',
            's_created_user_id' => 'Created User ID',
            's_updated_user_id' => 'Updated User ID',
            's_created_dt' => 'Created Dt',
            's_updated_dt' => 'Updated Dt',
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
                    ActiveRecord::EVENT_BEFORE_INSERT => ['s_created_dt'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['s_updated_dt'],
                ],
                'value' => date('Y-m-d H:i:s') //new Expression('NOW()'),
            ],
            /*'user' => [
                'class' => BlameableBehavior::class,
                'createdByAttribute' => 's_created_user_id',
                'updatedByAttribute' => 's_updated_user_id',
            ],*/
        ];

    }

    /**
     * @return mixed|string
     */
    public function getStatusName()
    {
        return self::STATUS_LIST[$this->s_status_id] ?? '-';
    }

    /**
     * @return mixed|string
     */
    public function getPriorityName()
    {
        return self::PRIORITY_LIST[$this->s_priority] ?? '-';
    }

    /**
     * @return mixed|string
     */
    public function getTypeName()
    {
        return self::TYPE_LIST[$this->s_type_id] ?? '-';
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSCreatedUser()
    {
        return $this->hasOne(Employee::class, ['id' => 's_created_user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSLanguage()
    {
        return $this->hasOne(Language::class, ['language_id' => 's_language_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSLead()
    {
        return $this->hasOne(Lead::class, ['id' => 's_lead_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSProject()
    {
        return $this->hasOne(Project::class, ['id' => 's_project_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSTemplateType()
    {
        return $this->hasOne(SmsTemplateType::class, ['stp_id' => 's_template_type_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSUpdatedUser()
    {
        return $this->hasOne(Employee::class, ['id' => 's_updated_user_id']);
    }

    /**
     * {@inheritdoc}
     * @return SmsQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new SmsQuery(get_called_class());
    }


    public function sendSms()
    {
        $out = ['error' => false];

        /** @var CommunicationService $communication */
        $communication = Yii::$app->communication;
        $data = [];
        $data['project_id'] = $this->s_project_id;

        $content_data['sms_text'] = $this->s_sms_text;

        $tplType = $this->sTemplateType ? $this->sTemplateType->stp_key : null;


        try {

            $str = 'ProjectId: ' . $this->s_project_id. ' TemplateKey:'. $tplType . ' From:' . $this->s_phone_from . ' To:'. $this->s_phone_to;
            //VarDumper::dump($str); exit;

            $request = $communication->smsSend($this->s_project_id, $tplType, $this->s_phone_from, $this->s_phone_to, $content_data, $data, ($this->s_language_id ?: 'en-US'), 0);

            if($request && isset($request['data']['sq_status_id'])) {
                $this->s_status_id = $request['data']['sq_status_id'];
                $this->s_communication_id = $request['data']['sq_id'];
                $this->save();
            }

            //VarDumper::dump($request, 10, true); exit;

            if($request && isset($request['error']) && $request['error']) {
                $this->s_status_id = self::STATUS_ERROR;
                $errorData = @json_decode($request['error'], true);
                $this->s_error_message = 'Communication error: ' . ($errorData['message'] ?: $request['error']);
                $this->save();
                $out['error'] = $this->s_error_message;
                Yii::error($str. "\r\n". $out['error'], 'Sms:sendSms:smsSend:CommunicationError');
            }

        } catch (\Throwable $exception) {
            $error = VarDumper::dumpAsString($exception->getMessage());
            $out['error'] = $error;
            Yii::error($str. "\r\n". $error, 'Sms:sendSms:smsSend:exception');
            $this->s_error_message = 'Communication error: ' . $error;
            $this->save();
        }

       // VarDumper::dump($request, 10, true); exit;

        return $out;
    }


    /**
     * @return int|mixed
     */
    public function detectLeadId()
    {

        $clientPhone = ClientPhone::find()->where(['phone' => $this->s_phone_from])->orderBy(['id' => SORT_DESC])->limit(1)->one();
        if($clientPhone && $clientPhone->client_id) {
            $lead = Lead::find()->where(['client_id' => $clientPhone->client_id, 'status' => [Lead::STATUS_PROCESSING, Lead::STATUS_SNOOZE, Lead::STATUS_ON_HOLD, Lead::STATUS_FOLLOW_UP]])->orderBy(['id' => SORT_DESC])->limit(1)->one();
            if(!$lead) {
                $lead = Lead::find()->where(['client_id' => $clientPhone->client_id])->orderBy(['id' => SORT_DESC])->limit(1)->one();
            }
            if($lead) {
                $this->s_lead_id = $lead->id;
            }
        }

        return $this->s_lead_id;
    }

}
