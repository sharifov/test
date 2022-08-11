<?php

namespace common\models;

use common\components\ChartTools;
use common\components\CommunicationService;
use common\components\jobs\LeadPoorProcessingRemoverJob;
use common\components\jobs\SmsOutEndedJob;
use common\components\jobs\SmsPriceJob;
use common\components\jobs\UserTaskCompletionJob;
use common\models\query\SmsQuery;
use DateTime;
use modules\lead\src\services\LeadTaskListService;
use modules\taskList\src\entities\TargetObject;
use modules\taskList\src\entities\TaskObject;
use modules\twilio\components\TwilioCommunicationService;
use src\behaviors\metric\MetricSmsCounterBehavior;
use src\entities\cases\Cases;
use src\entities\EventTrait;
use src\events\sms\IncomingSmsCreatedByLeadTypeEvent;
use src\events\sms\IncomingSmsCreatedByCaseTypeEvent;
use src\events\sms\SmsCreatedEvent;
use src\model\leadPoorProcessing\service\LeadPoorProcessingService;
use src\model\leadPoorProcessing\service\rules\LeadPoorProcessingNoAction;
use src\model\leadPoorProcessingData\entity\LeadPoorProcessingDataDictionary;
use src\model\leadPoorProcessingLog\entity\LeadPoorProcessingLogStatus;
use src\services\sms\incoming\SmsIncomingForm;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
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
 * @property bool $s_is_deleted
 * @property bool $s_is_new
 * @property int $s_delay
 * @property int $s_priority
 * @property int $s_status_id
 * @property string $s_status_done_dt
 * @property string $s_read_dt
 * @property string $s_error_message
 * @property float $s_tw_price
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
 * @property int $s_case_id
 * @property int $s_client_id
 *
 * @property Employee $sCreatedUser
 * @property Cases $sCase
 * @property Language $sLanguage
 * @property Lead $sLead
 * @property Project $sProject
 * @property SmsTemplateType $sTemplateType
 * @property Employee $sUpdatedUser
 * @property Client $client
 */
class Sms extends \yii\db\ActiveRecord
{
    use EventTrait;

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
    public const STATUS_SENT    = 7;
    public const STATUS_QUEUED  = 8;

    public const STATUS_LIST = [
        self::STATUS_NEW        => 'New',
        self::STATUS_PENDING    => 'Pending',
        self::STATUS_PROCESS    => 'Process',
        self::STATUS_CANCEL     => 'Cancel',
        self::STATUS_DONE       => 'Done',
        self::STATUS_ERROR      => 'Error',
        self::STATUS_SENT       => 'Sent',
        self::STATUS_QUEUED     => 'Queued',
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
     * @return static
     */
    private static function create(): self
    {
        $sms = new static();
        $sms->recordEvent(new SmsCreatedEvent($sms));
        return $sms;
    }

    public static function createByIncomingDefault(
        SmsIncomingForm $form,
        ?int $clientId,
        ?int $ownerId,
        ?int $leadId,
        ?int $caseId
    ): self {
        $sms = self::create();
        $sms->s_lead_id = $leadId;
        $sms->s_case_id = $caseId;
        $sms->loadByIncoming($form, $clientId, $ownerId);
        return $sms;
    }

    public static function createIncomingByCaseType(
        SmsIncomingForm $form,
        ?int $clientId,
        ?int $ownerId,
        ?int $caseId
    ): self {
        $sms = self::create();
        $sms->loadByIncoming($form, $clientId, $ownerId);
        $sms->s_case_id = $caseId;
        $sms->recordEvent(new IncomingSmsCreatedByCaseTypeEvent($sms, $caseId, $form->si_phone_from, $form->si_phone_to, $form->si_sms_text));
        return $sms;
    }

    public static function createIncomingByLeadType(
        SmsIncomingForm $form,
        ?int $clientId,
        ?int $ownerId,
        ?int $leadId
    ): self {
        $sms = self::create();
        $sms->loadByIncoming($form, $clientId, $ownerId);
        $sms->s_lead_id = $leadId;
        $sms->recordEvent(new IncomingSmsCreatedByLeadTypeEvent($sms, $leadId, $form->si_phone_from, $form->si_phone_to, $form->si_sms_text));
        return $sms;
    }

    /**
     * @param SmsIncomingForm $form
     * @param int $clientId
     * @param int|null $ownerId
     */
    private function loadByIncoming(SmsIncomingForm $form, ?int $clientId, ?int $ownerId): void
    {
//        $this->s_communication_id = $form->si_id;
        $this->s_type_id = self::TYPE_INBOX;
        $this->s_status_id = self::STATUS_DONE;
        $this->s_is_new = true;
        $this->s_status_done_dt = $form->si_sent_dt;
        $this->s_phone_to = $form->si_phone_to;
        $this->s_phone_from = $form->si_phone_from;
        $this->s_project_id = $form->si_project_id;
        $this->s_sms_text = $form->si_sms_text;
        $this->s_created_dt = $form->si_created_dt;
        $this->s_tw_message_sid = $form->si_message_sid;
        $this->s_tw_num_segments = $form->si_num_segments;
        $this->s_tw_to_country = $form->si_to_country;
        $this->s_tw_to_state = $form->si_to_state;
        $this->s_tw_to_city = $form->si_to_city;
        $this->s_tw_to_zip = $form->si_to_zip;
        $this->s_tw_from_country = $form->si_from_country;
        $this->s_tw_from_city = $form->si_from_city;
        $this->s_tw_from_state = $form->si_from_state;
        $this->s_tw_from_zip = $form->si_from_zip;
        $this->s_client_id = $clientId;
        $this->s_created_user_id = $ownerId;
    }

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
            [['s_reply_id', 's_lead_id', 's_project_id', 's_type_id', 's_template_type_id', 's_communication_id', 's_delay', 's_priority', 's_status_id', 's_tw_num_segments', 's_created_user_id', 's_updated_user_id', 's_case_id'], 'integer'],
            [['s_is_new', 's_is_deleted'], 'boolean'],
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
            [['s_case_id'], 'exist', 'skipOnError' => true, 'targetClass' => Cases::class, 'targetAttribute' => ['s_case_id' => 'cs_id']],
            [['s_created_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['s_created_user_id' => 'id']],
            [['s_language_id'], 'exist', 'skipOnError' => true, 'targetClass' => Language::class, 'targetAttribute' => ['s_language_id' => 'language_id']],
            [['s_lead_id'], 'exist', 'skipOnError' => true, 'targetClass' => Lead::class, 'targetAttribute' => ['s_lead_id' => 'id']],
            [['s_project_id'], 'exist', 'skipOnError' => true, 'targetClass' => Project::class, 'targetAttribute' => ['s_project_id' => 'id']],
            [['s_template_type_id'], 'exist', 'skipOnError' => true, 'targetClass' => SmsTemplateType::class, 'targetAttribute' => ['s_template_type_id' => 'stp_id']],
            [['s_updated_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['s_updated_user_id' => 'id']],
            [['s_client_id'], 'exist', 'skipOnError' => true, 'targetClass' => Client::class, 'targetAttribute' => ['s_client_id' => 'id']],
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
            's_case_id' => 'Case ID',
            's_client_id' => 'Client ID',
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
            'metric' => [
                'class' => MetricSmsCounterBehavior::class,
            ],
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
     * @return array|string[]
     */
    public static function getSmsTypeList(): array
    {
        return self::TYPE_LIST;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSCase()
    {
        return $this->hasOne(Cases::class, ['cs_id' => 's_case_id']);
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

    public function getClient(): ActiveQuery
    {
        return $this->hasOne(Client::class, ['id' => 's_client_id']);
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
        return new SmsQuery(static::class);
    }


    public function sendSms()
    {
        $out = ['error' => false];

        /** @var CommunicationService $communication */
        $communication = Yii::$app->comms;
        $data = [];
        $data['project_id'] = $this->s_project_id;
        $data['s_id'] = $this->s_id;

        $content_data['sms_text'] = $this->s_sms_text;

        $tplType = $this->sTemplateType ? $this->sTemplateType->stp_key : null;


        try {
            $str = 'ProjectId: ' . $this->s_project_id . ' TemplateKey:' . $tplType . ' From:' . $this->s_phone_from . ' To:' . $this->s_phone_to;
            //VarDumper::dump($str); exit;

            $request = $communication->smsSend($this->s_project_id, $tplType, $this->s_phone_from, $this->s_phone_to, $content_data, $data, ($this->s_language_id ?: 'en-US'), 0);

            if ($request && isset($request['data']['sq_status_id'])) {
                $this->s_status_id          = $request['data']['sq_status_id'];
                $this->s_communication_id   = $request['data']['sq_id'];

                $this->s_tw_message_sid     = $request['data']['sq_tw_message_id'] ?? null;

                /*if(!$this->s_tw_message_sid) {
                    Yii::warning('Not init s_tw_message_sid, comId: '. $this->s_communication_id, 'sendSms:s_tw_message_sid' );
                }*/

                $this->s_tw_num_segments    = $request['data']['sq_tw_num_segments'] ?? null;
                $this->s_tw_account_sid     = $request['data']['sq_tw_account_sid'] ?? null;
                $this->s_tw_price           = $request['data']['sq_tw_price'] ?? null;
                $this->s_sms_data           = $request['data']['sq_sms_data'] ?? null;
                $this->s_is_new             = false;

                /**
                 * @property int $sq_id
                * @property int $sq_project_id
                * @property string $sq_phone_from
                * @property string $sq_phone_to
                * @property string $sq_sms_text
                * @property string $sq_sms_data
                * @property int $sq_type_id
                * @property string $sq_language_id
                * @property int $sq_priority
                * @property int $sq_status_id
                * @property int $sq_delay
                * @property string $sq_tw_message_id
                * @property float $sq_tw_price
                * @property int $sq_tw_num_segments
                * @property string $sq_tw_sent_dt
                * @property string $sq_tw_status
                * @property string $sq_tw_uri
                * @property string $sq_tw_account_sid
                * @property int $sq_created_api_user_id
                * @property string $sq_created_dt
                 */

                $this->save();
            }

            if ($request && isset($request['error']) && $request['error']) {
                $this->s_status_id = self::STATUS_ERROR;
                $errorData = @json_decode($request['error'], true);
                $this->s_error_message = 'Communication error: ' . ($errorData['message'] ?: $request['error']);
                $this->save();
                $out['error'] = $this->s_error_message;
                Yii::error($str . "\r\n" . $out['error'], 'Sms:sendSms:smsSend:CommunicationError');
            }

            if ($this->s_id) {
                $smsOutEndedJob = new SmsOutEndedJob($this->s_id);
                Yii::$app->queue_job->priority(10)->push($smsOutEndedJob);

                if (($lead = $this->sLead) && (new LeadTaskListService($lead))->isProcessAllowed()) {
                    $job = new UserTaskCompletionJob(
                        TargetObject::TARGET_OBJ_LEAD,
                        $lead->id,
                        TaskObject::OBJ_SMS,
                        $this->s_id,
                        $lead->employee_id
                    );
                    Yii::$app->queue_job->push($job);
                }
            }
        } catch (\Throwable $exception) {
            $error = VarDumper::dumpAsString($exception->getMessage());
            $out['error'] = $error;
            Yii::error($str . "\r\n" . $error, 'Sms:sendSms:smsSend:exception');
            $this->s_error_message = 'Communication error: ' . $error;
            $this->save();
        }

        return $out;
    }


    /**
     * @return int|mixed
     */
    public function detectLeadId()
    {

        $clientPhone = ClientPhone::find()->where(['phone' => $this->s_phone_from])->orderBy(['id' => SORT_DESC])->limit(1)->one();
        if ($clientPhone && $clientPhone->client_id) {
            $lead = Lead::find()->where(['client_id' => $clientPhone->client_id, 'status' => [Lead::STATUS_PROCESSING, Lead::STATUS_SNOOZE, Lead::STATUS_ON_HOLD, Lead::STATUS_FOLLOW_UP]])->orderBy(['id' => SORT_DESC])->limit(1)->one();
            if (!$lead) {
                $lead = Lead::find()->where(['client_id' => $clientPhone->client_id])->orderBy(['id' => SORT_DESC])->limit(1)->one();
            }
            if ($lead) {
                $this->s_lead_id = $lead->id;
            }
        }

        return $this->s_lead_id;
    }

    /**
     * @return array
     */
    public function getUsersIdByPhone(): array
    {
        $users = [];
//        $params = UserProjectParams::find()->where(['upp_tw_phone_number' => $this->s_phone_to])->all();
//
//        if($params) {
//            foreach ($params as $param) {
//                $users[$param->upp_user_id] = $param->upp_user_id;
//            }
//        }
        $params = UserProjectParams::find()->select(['upp_user_id'])->byPhone($this->s_phone_to, false)->asArray()->all();
        foreach ($params as $param) {
            $users[(int)$param['upp_user_id']] = (int)$param['upp_user_id'];
        }
        return $users;
    }

    /**
     * @param string $startDate
     * @param string $endDate
     * @param string|null $groupingBy
     * @param int $smsType
     * @return array
     * @throws \Exception
     */
    public static function getSmsStats(string $startDate, string $endDate, ?string $groupingBy, int $smsType): array
    {
        $sDate = $startDate . " 00:00:00";
        $eDate = $endDate . " 23:59:59";
        switch ($groupingBy) {
            case null:
                if (strtotime($startDate) == strtotime($endDate)) {
                    $hoursRange = ChartTools::getHoursRange($startDate, $endDate . " 23:59:59", $step = '+1 hour', $format = 'H:i:s');
                } else {
                    $daysRange = ChartTools::getDaysRange($startDate, $endDate);
                }
                break;
            case 'hours':
                if (strtotime($startDate) == strtotime($endDate)) {
                    $hoursRange = ChartTools::getHoursRange($startDate, $endDate . " 23:59:59", $step = '+1 hour', $format = 'H:i:s');
                } else {
                    $hoursRange = ChartTools::getHoursRange($startDate, $endDate . " 23:59:59", $step = '+1 hour', $format = 'Y-m-d H:i:s');
                }
                break;
            case 'days':
                $daysRange = ChartTools::getDaysRange($startDate, $endDate);
                break;
            case 'weeks':
                $weeksPeriods = ChartTools::getWeeksRange(new DateTime($startDate), new DateTime($endDate . ' 23:59'));
                break;
            case 'months':
                $monthsRange = ChartTools::getMonthsRange($startDate, $endDate);
                $sDate = date("Y-m-01", strtotime($startDate));
                $eDate = date('Y-m-31', strtotime($endDate));
                break;
        }
        if ($smsType == 0) {
            $sms = self::find()->select(['s_status_id', 's_updated_dt', 's_tw_price'])
                ->where(['s_status_id' => [ self::STATUS_DONE, self::STATUS_ERROR]])
                ->andWhere(['between', 's_updated_dt', $sDate, $eDate])
                ->all();
        } else {
            $sms = self::find()->select(['s_status_id', 's_updated_dt', 's_tw_price'])
                ->where(['s_status_id' => [ self::STATUS_DONE, self::STATUS_ERROR]])
                ->andWhere(['between', 's_updated_dt', $sDate, $eDate])
                ->andWhere(['=', 's_type_id', $smsType])
                ->all();
        }

        $smsStats = [];
        $item = [];
        if (strtotime($startDate) < strtotime($endDate)) {
            if (isset($daysRange)) {
                $timeLine = $daysRange;
                $item['timeLine'] = 'd M';
                $timeInSeconds = 0;
                $dateFormat = 'Y-m-d';
            } elseif (isset($monthsRange)) {
                $timeLine = $monthsRange;
                $timeInSeconds = 0;
                $dateFormat = 'Y-m';
                $item['timeLine'] = 'Y, M';
            } elseif (isset($weeksPeriods)) {
                $timeLine = $weeksPeriods;
                $item['timeLine'] = 'd M';
                $timeInSeconds = 0;
                $dateFormat = 'Y-m-d';
            } elseif (isset($hoursRange)) {
                $timeLine = $hoursRange;
                $item['timeLine'] = 'H:i';
                $dateFormat = 'Y-m-d H:i:s';
                $timeInSeconds = 3600;
            }
        } else {
            if (isset($daysRange)) {
                $timeLine = $daysRange;
                $item['timeLine'] = 'd M';
                $timeInSeconds = 0;
                $dateFormat = 'Y-m-d';
            } elseif (isset($hoursRange)) {
                $timeLine = $hoursRange;
                $item['timeLine'] = 'H:i';
                $dateFormat = 'H:i:s';
                $timeInSeconds = 3600;
            } elseif (isset($monthsRange)) {
                $timeLine = $monthsRange;
                $timeInSeconds = 0;
                $dateFormat = 'Y-m';
                $item['timeLine'] = 'Y, M';
            } elseif (isset($weeksPeriods)) {
                $timeLine = $weeksPeriods;
                $item['timeLine'] = 'd M';
                $timeInSeconds = 0;
                $dateFormat = 'Y-m-d';
            }
        }

        $done = $error = $sd_TotalPrice = 0;
        foreach ($timeLine as $key => $timeSignature) {
            $weekInterval = explode('/', $timeSignature);
            if (count($weekInterval) != 2) {
                $EndPoint = date($dateFormat, strtotime($timeSignature) + $timeInSeconds);
                if ($EndPoint == '00:00:00') {
                    $EndPoint = '23:59:59';
                }
            } else {
                $EndPoint = date($dateFormat, strtotime($weekInterval[1]));
                $timeSignature = date($dateFormat, strtotime($weekInterval[0]));
            }
            foreach ($sms as $smsItem) {
                $smsUpdatedTime = date($dateFormat, strtotime($smsItem->s_updated_dt));
                if ($smsUpdatedTime >= $timeSignature && $smsUpdatedTime <= $EndPoint) {
                    switch ($smsItem->s_status_id) {
                        case self::STATUS_DONE:
                            $sd_TotalPrice = $sd_TotalPrice + $smsItem->s_tw_price;
                            $done++;
                            break;
                        case self::STATUS_ERROR:
                            $error++;
                            break;
                    }
                }
            }
            $item['time'] = $timeSignature;
            $item['weeksInterval'] = (count($weekInterval) == 2) ? $EndPoint : null;
            $item['done'] = $done;
            $item['error'] = $error;
            $item['sd_TotalPrice'] = round($sd_TotalPrice, 2);

            array_push($smsStats, $item);
            $done = $error = $sd_TotalPrice = 0;
        }
        return $smsStats;
    }


    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        if ($this->s_lead_id && $this->sLead) {
            $this->sLead->updateLastAction(LeadPoorProcessingLogStatus::REASON_SMS);
        }
        if ($this->s_case_id && $this->sCase) {
            $this->sCase->updateLastAction();
        }

        $isChangedStatus = array_key_exists('s_status_id', $changedAttributes);

        if (($isChangedStatus || ($insert && $this->isIn())) && $this->s_tw_message_sid && $this->isEnded()) {
            $createJob = (bool)(Yii::$app->params['settings']['sms_price_job'] ?? false);
            if ($createJob) {
                $delayJob = 60;
                $job = new SmsPriceJob();
                $job->smsSids = [$this->s_tw_message_sid];
                $job->delayJob = $delayJob;
                Yii::$app->queue_job->delay($delayJob)->priority(10)->push($job);
            }
        }
    }

    public function isOut(): bool
    {
        return $this->s_type_id === self::TYPE_OUTBOX;
    }

    public function isIn(): bool
    {
        return $this->s_type_id === self::TYPE_INBOX;
    }

    public function isDraft(): bool
    {
        return $this->s_type_id === self::TYPE_DRAFT;
    }

    public function setPrice($price): void
    {
        $this->s_tw_price = $price;
    }

    public function isDone(): bool
    {
        return $this->s_status_id === self::STATUS_DONE;
    }

    public function isCancel(): bool
    {
        return $this->s_status_id === self::STATUS_CANCEL;
    }

    public function isError(): bool
    {
        return $this->s_status_id === self::STATUS_ERROR;
    }

    public function isEnded(): bool
    {
        return $this->isDone() || $this->isCancel() || $this->isError();
    }
}
