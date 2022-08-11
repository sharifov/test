<?php

namespace common\models;

use common\components\jobs\CallOutEndedJob;
use common\components\jobs\CallPriceJob;
use common\components\jobs\CheckClientCallJoinToConferenceJob;
use common\components\jobs\LeadPoorProcessingRemoverJob;
use common\components\jobs\UserTaskCompletionJob;
use common\components\purifier\Purifier;
use common\models\query\CallQuery;
use modules\featureFlag\FFlag;
use modules\lead\src\services\LeadTaskListService;
use modules\product\src\entities\productQuoteChange\ProductQuoteChange;
use modules\taskList\src\entities\TargetObject;
use modules\taskList\src\entities\TaskObject;
use src\behaviors\metric\MetricCallCounterBehavior;
use src\helpers\app\AppHelper;
use src\helpers\DuplicateExceptionChecker;
use src\helpers\PhoneFormatter;
use src\helpers\setting\SettingHelper;
use src\model\call\entity\call\data\CreatorType;
use src\model\call\entity\call\data\Data;
use src\model\call\entity\call\data\QueueLongTime;
use src\model\call\entity\call\data\Repeat;
use src\model\call\helper\CallHelper;
use frontend\widgets\newWebPhone\call\socket\MissedCallMessage;
use frontend\widgets\newWebPhone\call\socket\RemoveIncomingRequestMessage;
use frontend\widgets\notification\NotificationMessage;
use src\access\EmployeeDepartmentAccess;
use src\dispatchers\NativeEventDispatcher;
use src\entities\cases\Cases;
use src\entities\cases\CasesStatus;
use src\entities\EventTrait;
use src\events\call\CallCreatedEvent;
use src\helpers\cases\CasesUrlHelper;
use src\helpers\lead\LeadUrlHelper;
use src\model\call\entity\call\events\CallEvents;
use src\model\call\services\FriendlyName;
use src\model\call\services\RecordManager;
use src\model\call\socket\CallUpdateMessage;
use src\model\callLog\services\CallLogTransferService;
use src\model\client\notifications\ClientNotificationCanceler;
use src\model\callLogFilterGuard\entity\CallLogFilterGuard;
use src\model\conference\service\ConferenceDataService;
use src\model\leadBusinessExtraQueue\service\LeadBusinessExtraQueueService;
use src\model\leadBusinessExtraQueueLog\entity\LeadBusinessExtraQueueLogStatus;
use src\model\leadPoorProcessing\service\LeadPoorProcessingService;
use src\model\leadPoorProcessingData\entity\LeadPoorProcessingDataDictionary;
use src\model\leadPoorProcessingData\entity\LeadPoorProcessingDataQuery;
use src\model\leadPoorProcessingLog\entity\LeadPoorProcessingLogStatus;
use src\model\leadUserConversion\service\LeadUserConversionDictionary;
use src\model\leadUserConversion\service\LeadUserConversionService;
use src\model\phoneList\entity\PhoneList;
use src\model\user\entity\userStatus\UserStatus;
use src\repositories\cases\CasesRepository;
use src\repositories\lead\LeadRepository;
use src\services\cases\CasesManageService;
use src\services\lead\qcall\Config;
use src\services\lead\qcall\FindPhoneParams;
use src\services\lead\qcall\FindWeightParams;
use src\services\lead\qcall\QCallService;
use thamtech\uuid\helpers\UuidHelper;
use webapi\src\services\communication\RequestDataDTO;
use Yii;
use DateTime;
use common\components\ChartTools;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\VarDumper;
use DateTimeZone;
use Locale;

/**
 * This is the model class for table "call".
 *
 * @property int $c_id
 * @property string $c_call_sid
 * @property int $c_call_type_id
 * @property string $c_from
 * @property string $c_to
 * @property string $c_call_status
 * @property string $c_forwarded_from
 * @property string $c_caller_name
 * @property string $c_parent_call_sid
 * @property int $c_call_duration
 * @property int $c_recording_duration
 * @property int $c_sequence_number
 * @property int $c_lead_id
 * @property int $c_created_user_id
 * @property string $c_created_dt
 * @property int $c_com_call_id
 * @property string $c_updated_dt
 * @property int $c_project_id
 * @property string $c_error_message
 * @property float $c_price
 * @property int $c_source_type_id
 * @property int $c_dep_id
 * @property int $c_case_id
 * @property int $c_client_id
 * @property int $c_status_id
 * @property int|null $c_parent_id
 * @property string $c_recording_sid
 * @property int $c_source_id
 * @property string $c_offset_gmt
 * @property string $c_from_country
 * @property string $c_from_state
 * @property string $c_from_city
 * @property bool $c_is_transfer
 * @property string $c_queue_start_dt
 * @property int|null $c_group_id
 * @property string|null $c_conference_sid
 * @property int|null $c_conference_id
 * @property string $c_is_conference
 * @property string|null $c_language_id
 * @property string|null $c_stir_status
 *
 * @property string $c_recording_url
 * @property bool $c_is_new
 * @property string|null $c_data_json
 * @property string $recordingUrl
 * @property bool $c_recording_disabled
 *
 * @property Data|null $data
 *
 * @property Employee $cCreatedUser
 * @property Cases $cCase
 * @property Client $cClient
 * @property Department $cDep
 * @property Lead $cLead
 * @property Language $cLanguage
 * @property Call $cParent
 * @property Call[] $calls
 * @property Project $cProject
 * @property CallUserAccess[] $callUserAccesses
 * @property Employee[] $cuaUsers
 * @property CallUserGroup[] $callUserGroups
 * @property UserGroup[] $cugUgs
 * @property Cases[] $cases
 * @property ConferenceParticipant[] $conferenceParticipants
 * @property ConferenceParticipant $currentParticipant
 * @property Conference[] $conferences
 * @property CallLogFilterGuard $callLogFilterGuard
 */
class Call extends \yii\db\ActiveRecord
{
    use EventTrait;

    public const TW_STATUS_IVR            = 'ivr';
    public const TW_STATUS_QUEUE          = 'queued';
    public const TW_STATUS_RINGING        = 'ringing';
    public const TW_STATUS_IN_PROGRESS    = 'in-progress';
    public const TW_STATUS_COMPLETED      = 'completed';
    public const TW_STATUS_BUSY           = 'busy';
    public const TW_STATUS_NO_ANSWER      = 'no-answer';
    public const TW_STATUS_FAILED         = 'failed';
    public const TW_STATUS_CANCELED       = 'canceled';

    public const TW_STATUS_LIST = [
        self::TW_STATUS_QUEUE           => 'Queued',
        self::TW_STATUS_RINGING         => 'Ringing',
        self::TW_STATUS_IN_PROGRESS     => 'In progress',
        self::TW_STATUS_COMPLETED       => 'Completed',
        self::TW_STATUS_BUSY            => 'Busy',
        self::TW_STATUS_NO_ANSWER       => 'No answer',
        self::TW_STATUS_FAILED          => 'Failed',
        self::TW_STATUS_CANCELED        => 'Canceled',
    ];


    public const STATUS_IVR            = 1;
    public const STATUS_QUEUE          = 2;
    public const STATUS_RINGING        = 3;
    public const STATUS_IN_PROGRESS    = 4;
    public const STATUS_COMPLETED      = 5;
    public const STATUS_BUSY           = 6;
    public const STATUS_NO_ANSWER      = 7;
    public const STATUS_FAILED         = 8;
    public const STATUS_CANCELED       = 9;
    public const STATUS_DELAY          = 10;
    public const STATUS_DECLINED       = 11;
    public const STATUS_HOLD           = 12;

    public const STATUS_LIST = [
        self::STATUS_IVR           => 'IVR',
        self::STATUS_QUEUE         => 'Queued',
        self::STATUS_RINGING       => 'Ringing',
        self::STATUS_IN_PROGRESS   => 'In progress',
        self::STATUS_COMPLETED     => 'Completed',
        self::STATUS_BUSY          => 'Busy',
        self::STATUS_NO_ANSWER     => 'No answer',
        self::STATUS_FAILED        => 'Failed',
        self::STATUS_CANCELED      => 'Canceled',
        self::STATUS_DELAY         => 'Delay',
        self::STATUS_DECLINED      => 'Declined',
        self::STATUS_HOLD          => 'Hold',
    ];


    public const STATUS_LABEL_LIST = [
        self::STATUS_IVR            => '<span class="label label-warning"><i class="fa fa-refresh fa-spin"></i> ' . self::STATUS_LIST[self::STATUS_IVR] . '</span>',
        self::STATUS_QUEUE          => '<span class="label label-warning"><i class="fa fa-refresh fa-spin"></i> ' . self::STATUS_LIST[self::STATUS_QUEUE] . '</span>',
        self::STATUS_RINGING        => '<span class="label label-warning"><i class="fa fa-spinner fa-spin"></i> ' . self::STATUS_LIST[self::STATUS_RINGING] . '</span>',
        self::STATUS_IN_PROGRESS    => '<span class="label label-success"><i class="fa fa-volume-control-phone"></i> ' . self::STATUS_LIST[self::STATUS_IN_PROGRESS] . '</span>',
        self::STATUS_COMPLETED      => '<span class="label label-info"><i class="fa fa-check"></i> ' . self::STATUS_LIST[self::STATUS_COMPLETED] . '</span>',
        self::STATUS_BUSY           => '<span class="label label-danger"><i class="fa fa-ban"></i> ' . self::STATUS_LIST[self::STATUS_BUSY] . '</span>',
        self::STATUS_NO_ANSWER      => '<span class="label label-danger"><i class="fa fa-times-circle"></i> ' . self::STATUS_LIST[self::STATUS_NO_ANSWER] . '</span>',
        self::STATUS_FAILED         => '<span class="label label-danger"><i class="fa fa-window-close"></i> ' . self::STATUS_LIST[self::STATUS_FAILED] . '</span>',
        self::STATUS_CANCELED       => '<span class="label label-danger"><i class="fa fa-close"></i> ' . self::STATUS_LIST[self::STATUS_CANCELED] . '</span>',
        self::STATUS_DELAY          => '<span class="label label-danger"><i class="fa fa-pause"></i> ' . self::STATUS_LIST[self::STATUS_DELAY] . '</span>',
        self::STATUS_DECLINED       => '<span class="label label-danger"><i class="fa fa-pause"></i> ' . self::STATUS_LIST[self::STATUS_DECLINED] . '</span>',
        self::STATUS_HOLD           => '<span class="label label-warning"><i class="fa fa-pause"></i> ' . self::STATUS_LIST[self::STATUS_HOLD] . '</span>',
    ];


    public const CALL_TYPE_OUT  = 1;
    public const CALL_TYPE_IN   = 2;
    public const CALL_TYPE_JOIN   = 3;
    public const CALL_TYPE_RETURN   = 4;

    public const TYPE_LIST = [
        self::CALL_TYPE_OUT => 'Outgoing',
        self::CALL_TYPE_IN  => 'Incoming',
        self::CALL_TYPE_JOIN  => 'Join',
        self::CALL_TYPE_RETURN  => 'Return',
    ];


    public const SOURCE_GENERAL_LINE    = 1;
    public const SOURCE_DIRECT_CALL     = 2;
    public const SOURCE_REDIRECT_CALL   = 3;
    public const SOURCE_TRANSFER_CALL   = 4;
    public const SOURCE_CONFERENCE_CALL = 5;
    public const SOURCE_REDIAL_CALL     = 6;
    public const SOURCE_LISTEN          = 7;
    public const SOURCE_COACH           = 8;
    public const SOURCE_BARGE           = 9;
    public const SOURCE_INTERNAL        = 10;
    public const SOURCE_LEAD        = 11;
    public const SOURCE_CASE        = 12;
    public const SOURCE_CLIENT_NOTIFICATION = 13;

    public const SOURCE_LIST = [
        self::SOURCE_GENERAL_LINE => 'General Line',
        self::SOURCE_DIRECT_CALL  => 'Direct Call',
        self::SOURCE_REDIRECT_CALL  => 'Redirect Call',
        self::SOURCE_TRANSFER_CALL  => 'Transfer Call',
        self::SOURCE_CONFERENCE_CALL  => 'Conference Call',
        self::SOURCE_REDIAL_CALL  => 'Redial Call',
        self::SOURCE_LISTEN  => 'Listen',
        self::SOURCE_COACH  => 'Coach',
        self::SOURCE_BARGE  => 'Barge',
        self::SOURCE_INTERNAL  => 'Internal',
        self::SOURCE_LEAD  => 'Lead',
        self::SOURCE_CASE  => 'Case',
        self::SOURCE_CLIENT_NOTIFICATION  => 'Client notification',
    ];

    public const SHORT_SOURCE_LIST = [
        self::SOURCE_GENERAL_LINE => 'General',
        self::SOURCE_DIRECT_CALL  => 'Direct',
        self::SOURCE_REDIRECT_CALL  => 'Redirect',
        self::SOURCE_TRANSFER_CALL  => 'Transfer',
        self::SOURCE_CONFERENCE_CALL  => 'Conference',
        self::SOURCE_REDIAL_CALL  => 'Redial',
        self::SOURCE_LISTEN  => 'Listen',
        self::SOURCE_COACH  => 'Coach',
        self::SOURCE_BARGE  => 'Barge',
        self::SOURCE_INTERNAL  => 'Internal',
        self::SOURCE_LEAD  => 'Lead',
        self::SOURCE_CASE  => 'Case',
        self::SOURCE_CLIENT_NOTIFICATION  => 'Client notification',
    ];

    public const TW_RECORDING_STATUS_PAUSED = 'paused';
    public const TW_RECORDING_STATUS_IN_PROGRESS = 'in-progress';
    public const TW_RECORDING_STATUS_STOPPED = 'stopped';

    public const QUEUE_IN_PROGRESS = 'inProgress';
    public const QUEUE_HOLD = 'hold';
    public const QUEUE_GENERAL = 'general';
    public const QUEUE_DIRECT = 'direct';

    public const CHANNEL_REALTIME_MAP = 'realtimeMapChannel';
    public const CHANNEL_USER_ONLINE = 'userOnlineChannel';

    public const DEFAULT_PRIORITY_VALUE = 0;

    public const STIR_STATUS_FULL = 'A';
    public const STIR_STATUS_FULL_FAILED = 'AF';
    public const STIR_STATUS_PARTIAL = 'B';
    public const STIR_STATUS_PARTIAL_FAILED = 'BF';
    public const STIR_STATUS_GATEWAY = 'C';
    public const STIR_STATUS_GATEWAY_FAILED = 'CF';
    public const STIR_STATUS_NO_VALIDATION = 'NV';
    public const STIR_STATUS_VALIDATION_FAILED = 'VF';

    public const STIR_STATUS_LIST = [
        self::STIR_STATUS_FULL => 'Full Attestation (A)',
        self::STIR_STATUS_PARTIAL => 'Partial Attestation (B)',
        self::STIR_STATUS_GATEWAY => 'Gateway Attestation (C)',
        self::STIR_STATUS_FULL_FAILED => 'Full Attestation (AF) Failed',
        self::STIR_STATUS_PARTIAL_FAILED => 'Partial Attestation (BF) Failed',
        self::STIR_STATUS_GATEWAY_FAILED => 'Gateway Attestation (CF) Failed',
        self::STIR_STATUS_NO_VALIDATION => '(NV) No Validation',
        self::STIR_STATUS_VALIDATION_FAILED => '(VF) Validation Failed',
    ];

    public const STIR_VERSTAT_LIST = [
        'TN-Validation-Passed-A' => self::STIR_STATUS_FULL,
        'TN-Validation-Passed-B' => self::STIR_STATUS_PARTIAL,
        'TN-Validation-Passed-C' => self::STIR_STATUS_GATEWAY,
        'TN-Validation-Failed-A' => self::STIR_STATUS_FULL_FAILED,
        'TN-Validation-Failed-B' => self::STIR_STATUS_PARTIAL_FAILED,
        'TN-Validation-Failed-C' => self::STIR_STATUS_GATEWAY_FAILED,
        'No-TN-Validation' => self::STIR_STATUS_NO_VALIDATION,
        'TN-Validation-Failed' => self::STIR_STATUS_VALIDATION_FAILED,
        'A' => self::STIR_STATUS_FULL,
        'B' => self::STIR_STATUS_PARTIAL,
        'C' => self::STIR_STATUS_GATEWAY
    ];

    public const STIR_TRUSTED_GROUP = [
        'TN-Validation-Passed-A',
        'TN-Validation-Passed-B'
    ];

    private ?Data $data = null;

    //public $c_recording_url = '';

    /**
     * @return string
     */
    public static function tableName(): string
    {
        return 'call';
    }

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['c_call_sid'], 'required'],
            [['c_call_type_id', 'c_lead_id', 'c_created_user_id', 'c_com_call_id', 'c_project_id', 'c_call_duration', 'c_recording_duration', 'c_dep_id', 'c_case_id', 'c_client_id', 'c_status_id', 'c_parent_id', 'c_sequence_number'], 'integer'],

            [['c_status_id','c_parent_id', 'c_dep_id', 'c_case_id', 'c_client_id', 'c_lead_id', 'c_call_type_id', 'c_call_duration', 'c_created_user_id', 'c_com_call_id', 'c_project_id', 'c_sequence_number', 'c_recording_duration', 'c_call_duration'],
                'filter', 'filter' => 'intval', 'skipOnEmpty' => true],

            [['c_is_new'], 'filter', 'filter' => 'intval', 'skipOnEmpty' => true],

            ['c_price', 'default', 'value' => null],
            [['c_price'], 'number'],
            [['c_is_new'], 'default', 'value' => true],
            [['c_case_id', 'c_lead_id', 'c_recording_duration', 'c_dep_id', 'c_client_id', 'c_call_duration'], 'default', 'value' => null],

            [['c_is_new'], 'boolean'],
            [['c_created_dt', 'c_updated_dt'], 'safe'],
            [['c_call_sid', 'c_parent_call_sid', 'c_recording_sid'], 'string', 'max' => 34],
            [['c_from', 'c_to', 'c_forwarded_from'], 'string', 'max' => 100],
            [['c_call_status'], 'string', 'max' => 15],
            [['c_caller_name'], 'string', 'max' => 50],
            //[['c_recording_url'], 'string', 'max' => 200],
            [['c_error_message'], 'string', 'max' => 500],
            [['c_language_id'], 'string', 'max' => 5],
            [['c_language_id'], 'exist', 'skipOnError' => true, 'targetClass' => Language::class, 'targetAttribute' => ['c_language_id' => 'language_id']],
            [['c_case_id'], 'exist', 'skipOnError' => true, 'targetClass' => Cases::class, 'targetAttribute' => ['c_case_id' => 'cs_id']],
            [['c_client_id'], 'exist', 'skipOnError' => true, 'targetClass' => Client::class, 'targetAttribute' => ['c_client_id' => 'id']],
            [['c_created_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['c_created_user_id' => 'id']],
            [['c_dep_id'], 'exist', 'skipOnError' => true, 'targetClass' => Department::class, 'targetAttribute' => ['c_dep_id' => 'dep_id']],
            [['c_lead_id'], 'exist', 'skipOnError' => true, 'targetClass' => Lead::class, 'targetAttribute' => ['c_lead_id' => 'id']],
            [['c_parent_id'], 'exist', 'skipOnError' => true, 'targetClass' => self::class, 'targetAttribute' => ['c_parent_id' => 'c_id']],
            [['c_project_id'], 'exist', 'skipOnError' => true, 'targetClass' => Project::class, 'targetAttribute' => ['c_project_id' => 'id']],

            ['c_is_transfer', 'default', 'value' => false],
            ['c_is_transfer', 'boolean'],

            ['c_queue_start_dt', 'datetime', 'format' => 'php:Y-m-d H:i:s'],

            ['c_group_id', 'integer'],

            ['c_conference_id', 'integer'],
            ['c_conference_id', 'exist', 'skipOnError' => true, 'targetClass' => Conference::class, 'targetAttribute' => ['c_conference_id' => 'cf_id']],

            ['c_conference_sid', 'string', 'max' => 34],

            ['c_is_conference', 'default', 'value' => false],
            ['c_is_conference', 'boolean'],

            ['c_data_json', 'string'],

            ['c_call_sid', 'unique'],

            ['c_recording_disabled', 'default', 'value' => false],
            ['c_recording_disabled', 'boolean'],

            ['c_stir_status', 'string'],
            ['c_stir_status', 'trim', 'skipOnEmpty' => true],
            ['c_stir_status', 'filter', 'filter' => 'strtoupper', 'skipOnEmpty' => true],
            ['c_stir_status', 'stirStatusProcessing'],
        ];
    }


    /**
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'c_id' => 'ID',
            'c_call_sid' => 'Call SID',
            'c_call_type_id' => 'Call Type ID',
            'c_from' => 'From',
            'c_to' => 'To',
            'c_call_status' => 'Twilio Status',
            'c_forwarded_from' => 'Forwarded From',
            'c_caller_name' => 'Caller Name',
            'c_parent_call_sid' => 'Parent Call SID',
            'c_call_duration' => 'Call Duration',
            'c_recording_url' => 'Recording Url',
            'c_recording_duration' => 'Recording Duration',
            'c_sequence_number' => 'Sequence Number',
            'c_lead_id' => 'Lead ID',
            'c_created_user_id' => 'Created User ID',
            'c_created_dt' => 'Created Dt',
            'c_com_call_id' => 'Com Call ID',
            'c_updated_dt' => 'Updated Dt',
            'c_project_id' => 'Project ID',
            'c_error_message' => 'Error Message',
            'c_is_new' => 'Is New',
            'c_price' => 'Price',
            'c_source_type_id' => 'Source Type',
            'c_dep_id' => 'Department ID',
            'c_case_id' => 'Case ID',
            'c_client_id' => 'Client',
            'c_status_id' => 'Status ID',
            'c_parent_id' => 'Parent ID',
            'c_recording_sid' => 'Recording SID',
            'c_is_transfer' => 'Transfer',
            'c_queue_start_dt' => 'Queue start dt',
            'c_group_id' => 'Group ID',
            'c_is_conference' => 'Is conference',
            'c_conference_id' => 'Conference ID',
            'c_conference_sid' => 'Conference SID',
            'c_language_id' => 'Language ID',
            'c_data_json' => 'Data',
            'c_recording_disabled' => 'Recording disabled',
            'c_stir_status' => 'Stir Status'
        ];
    }

    /**
     * @return array
     */
    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['c_created_dt', 'c_updated_dt'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['c_updated_dt'],
                ],
                'value' => date('Y-m-d H:i:s') //new Expression('NOW()'),
            ],
            'metric' => [
                'class' => MetricCallCounterBehavior::class,
            ],
        ];
    }

    public function stirStatusProcessing()
    {
        if (!empty($this->c_stir_status)) {
            if (!ArrayHelper::isIn($this->c_stir_status, array_keys(self::STIR_STATUS_LIST), true)) {
                $this->c_stir_status = null;
                \Yii::warning('Unregistered Stir Status (' . $this->c_stir_status . ')', 'Call:stirStatusProcessing');
            }
        }
    }

    /**
     * @return static
     */
    private static function create(): self
    {
        $call = new static();
        $call->recordEvent(new CallCreatedEvent($call));
        return $call;
    }

    /**
     * @param $callSid
     * @param $callTypeId
     * @param $from
     * @param $to
     * @param $createdDt
     * @param $comCallId
     * @param $offsetGmt
     * @param $fromCountry
     * @param $fromState
     * @param $fromCity
     * @param $createdUserId
     * @param $stirStatus
     * @return static
     */
    public static function createDeclined(
        $callSid,
        $callTypeId,
        $from,
        $to,
        $createdDt,
        $comCallId,
        $offsetGmt,
        $fromCountry,
        $fromState,
        $fromCity,
        $createdUserId,
        $stirStatus
    ): self {
        $call = self::create();
        $call->c_call_sid = $callSid;
        $call->c_call_type_id = $callTypeId;
        $call->c_from = $from;
        $call->c_to = $to;
        $call->c_com_call_id = $comCallId;
        $call->c_offset_gmt = $offsetGmt;
        $call->c_from_country = $fromCountry;
        $call->c_from_state = $fromState;
        $call->c_from_city = $fromCity;
        $call->c_created_user_id = $createdUserId;
        $call->c_is_new = true;
        $call->c_created_dt = $createdDt;
        $call->c_updated_dt = date('Y-m-d H:i:s');
        $call->c_stir_status = $stirStatus;
        $call->declined();
        return $call;
    }

    public static function createByIncoming(RequestDataDTO $requestDataDTO, ?int $projectId, ?int $depId, ?int $clientId): Call
    {
        $call = self::create();
        $call->c_call_sid = $requestDataDTO->CallSid ?? null;
        $call->c_call_type_id = self::CALL_TYPE_IN;
        $call->c_parent_call_sid = $requestDataDTO->ParentCallSid ?? null;
        $call->c_offset_gmt = self::getClientTime(ArrayHelper::toArray($requestDataDTO));
        $call->c_from_country = self::getDisplayRegion($requestDataDTO->FromCountry);
        $call->c_from_state = $requestDataDTO->FromState ?? null;
        $call->c_from_city = $requestDataDTO->FromCity ?? null;
        $call->c_is_new = true;
        $call->c_created_dt = date('Y-m-d H:i:s');
        $call->c_from = $requestDataDTO->From;
        $call->c_to = $requestDataDTO->To; //Called
        $call->c_created_user_id = null;
        $call->c_project_id = $projectId;
        $call->c_dep_id = $depId;
        $call->c_client_id = $clientId;
        $call->c_stir_status = self::getStirStatusByVerstatKey(ArrayHelper::getValue($requestDataDTO, 'callData.StirVerstat', ''));
        $call->setStatusIvr();
        return $call;
    }

    public function assignParentCall(int $callId, int $projectId, int $depId, int $sourceTypeId, ?int $stirStatus): void
    {
        $this->c_parent_id = $callId;
        $this->c_project_id = $projectId;
        $this->c_dep_id = $depId;
        $this->c_source_type_id = $sourceTypeId;
        $this->c_stir_status = $stirStatus;
    }

    /**
     * @return bool
     */
    public function isChild(): bool
    {
        return $this->c_parent_id !== null;
    }

    /**
     * @return bool
     */
    public function isGeneralParent(): bool
    {
        return $this->c_parent_id === null;
    }

    /**
     * @param string $recordingUrl
     * @param string $recordingSid
     * @param int $recordingDuration
     */
    public function updateRecordingData(string $recordingUrl, string $recordingSid, int $recordingDuration): void
    {
        //$this->c_recording_url = $recordingUrl;

        if ($recordingUrl) {
            preg_match('~(RE[0-9a-zA-Z]{32})$~', $recordingUrl, $math);
            if (!empty($math[1])) {
                $this->c_recording_sid = $math[1];
            }
        }

        $this->c_recording_duration = $recordingDuration;
        $this->c_updated_dt = date('Y-m-d H:i:s');
    }

    /**
     * @param int|null $userId
     */
    public function setCreatedUser(?int $userId): void
    {
        $this->c_created_user_id = $userId;
    }

    /**
     * @param int|null $projectId
     */
    public function setProject(?int $projectId): void
    {
        $this->c_project_id = $projectId;
    }

    /**
     * @param $price
     */
    public function setPrice($price): void
    {
        $this->c_price = $price;
    }

    /**
     * @return bool
     */
    public function isEmptyStatus(): bool
    {
        return $this->c_call_status ? false : true;
    }

    /**
     * @param string|null $status
     */
    public function setStatus(?string $status): void
    {
        $this->c_call_status = $status;
    }

    /**
     * @param int|null $duration
     */
    public function setDuration(?int $duration): void
    {
        $this->c_call_duration = $duration;
    }


    /**
     * @return ActiveQuery
     */
    public function getConferenceParticipants(): ActiveQuery
    {
        return $this->hasMany(ConferenceParticipant::class, ['cp_call_id' => 'c_id']);
    }

    public function getCurrentParticipant(): ActiveQuery
    {
        return $this->hasOne(ConferenceParticipant::class, ['cp_call_id' => 'c_id'])->orderBy(['cp_id' => SORT_DESC])->limit(1);
    }

    public function getConferences(): ActiveQuery
    {
        return $this->hasMany(Conference::class, ['cf_created_user_id' => 'c_created_user_id']);
    }


    /**
     * @return ActiveQuery
     */
    public function getCallUserAccesses(): ActiveQuery
    {
        return $this->hasMany(CallUserAccess::class, ['cua_call_id' => 'c_id']);
    }

    public function getCallLogFilterGuard(): ActiveQuery
    {
        return $this->hasOne(CallLogFilterGuard::class, ['clfg_call_id' => 'c_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getCuaUsers(): ActiveQuery
    {
        return $this->hasMany(Employee::class, ['id' => 'cua_user_id'])->viaTable('call_user_access', ['cua_call_id' => 'c_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getCallUserGroups(): ActiveQuery
    {
        return $this->hasMany(CallUserGroup::class, ['cug_c_id' => 'c_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getCugUgs(): ActiveQuery
    {
        return $this->hasMany(UserGroup::class, ['ug_id' => 'cug_ug_id'])->viaTable('call_user_group', ['cug_c_id' => 'c_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getCases(): ActiveQuery
    {
        return $this->hasMany(Cases::class, ['cs_call_id' => 'c_id']);
    }

    /**
     * @return ActiveQuery: ActiveQuery
     */
    public function getCCase(): ActiveQuery
    {
        return $this->hasOne(Cases::class, ['cs_id' => 'c_case_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getCClient(): ActiveQuery
    {
        return $this->hasOne(Client::class, ['id' => 'c_client_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getCDep(): ActiveQuery
    {
        return $this->hasOne(Department::class, ['dep_id' => 'c_dep_id']);
    }

    /**
     * Gets query for [[CLanguage]].
     *
     * @return ActiveQuery
     */
    public function getCLanguage(): ActiveQuery
    {
        return $this->hasOne(Language::class, ['language_id' => 'c_language_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getCCreatedUser(): ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 'c_created_user_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getCProject(): ActiveQuery
    {
        return $this->hasOne(Project::class, ['id' => 'c_project_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getCLead(): ActiveQuery
    {
        return $this->hasOne(Lead::class, ['id' => 'c_lead_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getCParent(): ActiveQuery
    {
        return $this->hasOne(self::class, ['c_id' => 'c_parent_id']);
    }

    /**
     * @return $this|null
     */
    public function getGrandParent(): ?self
    {
        $current = $this;
        $parent = null;
        while ($pCall = $current->cParent) {
            $current = $pCall;
            $parent = clone $pCall;
        }
        return $parent;
    }

    /**
     * @return ActiveQuery
     */
    public function getCalls()
    {
        return $this->hasMany(self::class, ['c_parent_id' => 'c_id']);
    }

    /**
     * {@inheritdoc}
     * @return CallQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new CallQuery(static::class);
    }

    /**
     * @return mixed|string
     */
    public function getCallTypeName()
    {
        return self::TYPE_LIST[$this->c_call_type_id] ?? '-';
    }

    public static function getCallTypeNameById(int $type)
    {
        return self::TYPE_LIST[$type] ?? '-';
    }

    /**
     * @return mixed|string
     */
    public function getTwilioStatusName()
    {
        return self::TW_STATUS_LIST[$this->c_call_status] ?? '-';
    }

    /**
     * @return mixed|string
     */
    public function getStatusName()
    {
        return self::STATUS_LIST[$this->c_status_id] ?? '-';
    }

    public static function getStatusNameById(?int $statusId): string
    {
        return self::STATUS_LIST[$statusId] ?? '-';
    }

    /**
     * @return string
     */
    public function getStatusIcon(): string
    {
        if ($this->isStatusRinging()) {
            $icon = 'fa fa-refresh fa-pulse fa-fw text-danger';
        } elseif ($this->isStatusInProgress()) {
            $icon = 'fa fa-spinner fa-pulse fa-fw';
        } elseif ($this->isStatusQueue()) {
            $icon = 'fa fa-pause';
        } elseif ($this->isStatusCompleted()) {
            $icon = 'fa fa-flag text-success';
        } elseif ($this->isStatusDelay()) {
            $icon = 'fa fa-pause text-success';
        } elseif ($this->isStatusCanceled() || $this->isStatusNoAnswer() || $this->isStatusBusy() || $this->isStatusFailed()) {
            $icon = 'fa fa-times-circle text-danger';
        } else {
            $icon = '';
        }

        return '<i class="' . $icon . '"></i>';
    }

    /**
     * @return mixed|string
     */
    public function getSourceName()
    {
        return self::SOURCE_LIST[$this->c_source_type_id] ?? '-';
    }

    public function getShortSourceName()
    {
        return self::SHORT_SOURCE_LIST[$this->c_source_type_id] ?? '-';
    }

    /**
     * @return mixed|string
     */
    public function getStatusLabel()
    {
        return self::STATUS_LABEL_LIST[$this->c_status_id] ?? '-';
    }


    /**
     * @param bool $insert
     * @param array $changedAttributes
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        $leadRepository = Yii::createObject(LeadRepository::class);
        $qCallService = Yii::createObject(QCallService::class);

//        $userListSocketNotification = [];
//        $isChangedStatus = isset($changedAttributes['c_status_id']);
        $isChangedStatus = array_key_exists('c_status_id', $changedAttributes);
        $isChangedStatusFromEmptyInclude = array_key_exists('c_status_id', $changedAttributes);

        if ($this->c_parent_id && $this->isOut() && ($lead = $this->cLead) && $lead->isCallPrepare()) {
            try {
                $lead->callProcessing();
                $leadRepository->save($lead);
            } catch (\Throwable $e) {
                Yii::error('CallId: ' . $this->c_id . ' LeadId: ' . $lead->id . ' Message: ' . $e->getMessage(), 'Call:afterSave:Lead:callProcessing');
            }
        }

        if ($this->c_parent_id === null && ($insert || $isChangedStatus) && $this->c_lead_id && $this->isOut() && $this->isEnded()) {
            if (($lead = $this->cLead) && !$lead->isCallReady()) {
                try {
                    $lead->callReady();
                    $leadRepository->save($lead);
                } catch (\Throwable $e) {
                    Yii::error('CallId: ' . $this->c_id . ' LeadId: ' . $lead->id . ' Message: ' . $e->getMessage(), 'Call:afterSave:Lead:callReady');
                }
            }
        }

        if ($this->c_parent_id && ($insert || $isChangedStatusFromEmptyInclude) && $this->c_lead_id && $this->isOut() && $this->isEnded()) {
            $lead = $this->cLead;

            $lqc = LeadQcall::findOne($this->c_lead_id);
            if ($lqc) {
                $timeFromValidationIsOk = true;
                $currentTime = time();
                if (SettingHelper::leadRedialQCallAttemptsFromTimeValidationEnabled()) {
                    $timeFromValidationIsOk = $currentTime >= strtotime($lqc->lqc_dt_from);
                }
                if ($timeFromValidationIsOk) {
                    $lf = LeadFlow::find()->where(['lead_id' => $this->c_lead_id])->orderBy(['id' => SORT_DESC])->limit(1)->one();
                    if ($lf) {
                        $lf->lf_out_calls = (int)$lf->lf_out_calls + 1;
                        if (!$lf->save()) {
                            Yii::error(VarDumper::dumpAsString($lf->errors), 'Call:afterSave:LeadFlow:update');
                        }

                        $attempts = 0;
                        try {
                            $attempts = (int)Yii::$app->params['settings']['redial_pending_to_follow_up_attempts'];
                        } catch (\Throwable $e) {
                            Yii::error($e, 'Not found redial_pending_to_follow_up_attempts setting');
                        }

                        if ($lf->lf_out_calls >= $attempts && $lead->isPending()) {
                            try {
                                $lead->followUp(null, null, 'Redial Pending max attempts reached');
                                $leadRepository->save($lead);
                                $qCallService->remove($lead->id);
                                $qCallService->create(
                                    $lead->id,
                                    new Config($lead->status, $lead->getCountOutCallsLastFlow()),
                                    new FindWeightParams($lead->project_id, $lead->status),
                                    $lead->offset_gmt,
                                    new FindPhoneParams($lead->project_id, $lead->l_dep_id)
                                );
                            } catch (\Throwable $e) {
                                Yii::error('CallId: ' . $this->c_id . ' LeadId: ' . $lead->id . ' Message: ' . $e->getMessage(), 'Call:AfterSave:Lead follow up');
                            }
                        }

                        try {
                            $attempts = (int)Yii::$app->params['settings']['redial_max_attempts_for_dates_passed'];
                            if (
                                $lf->lf_out_calls > $attempts
                                && ($departure = $lead->getDeparture())
                                && strtotime($departure) < time()
                            ) {
                                $qCallService->remove($lead->id);
                                $lead->trash($lead->employee_id, null, 'Travel Dates Passed');
                                $leadRepository->save($lead);
                            }
                        } catch (\Throwable $e) {
                            Yii::error($e, 'redial_max_attempts_for_dates_passed');
                        }
                    } else {
                        Yii::error([
                            'message' => 'Not found lead flow.',
                            'leadId' => $this->c_lead_id,
                            'callId' => $this->c_id,
                        ], 'LeadRedial');
                    }
                } else {
                    Yii::error([
                        'message' => 'Detected Redial Call finished with date time from validation error.',
                        'leadId' => $this->c_lead_id,
                        'callId' => $this->c_id,
                        'currentTime' => $currentTime,
                        'lqc_dt_from' => $lqc->lqc_dt_from,
                        'lqc_dt_from_to_time' => strtotime($lqc->lqc_dt_from),
                    ], 'LeadRedial');
                }
//            } elseif ($this->isRedialCall()) {
//                Yii::error([
//                    'message' => 'Detected Redial Call finished without LeadQCall record.',
//                    'leadId' => $this->c_lead_id,
//                    'callId' => $this->c_id,
//                ], 'LeadRedial');
            }

            if ($lead->leadQcall) {
                try {
                    $qCallService->updateInterval(
                        $lead->leadQcall,
                        new Config($lead->status, $lead->getCountOutCallsLastFlow()),
                        $lead->offset_gmt,
                        new FindPhoneParams($lead->project_id, $lead->l_dep_id),
                        new FindWeightParams($lead->project_id, $lead->status)
                    );
                } catch (\Throwable $e) {
                    Yii::error('CallId: ' . $this->c_id . ' LeadId: ' . $lead->id . ' Message: ' . $e->getMessage(), 'Call:AfterSave:QCallService:updateInterval');
                }
            }
        }

        if (!$insert) {
            if ($isChangedStatus && ($this->isStatusInProgress() || $this->isEnded())) {
                $callUserAccessAny = CallUserAccess::find()->where([
                    'cua_status_id' => [CallUserAccess::STATUS_TYPE_PENDING, CallUserAccess::STATUS_TYPE_WARM_TRANSFER],
                    'cua_call_id' => $this->c_id
                ])->all();
                if ($callUserAccessAny) {
                    foreach ($callUserAccessAny as $callAccess) {
                        $sendWarmTransferMissedNotification = $callAccess->isWarmTransfer();
                        $callAccess->noAnsweredCall();
                        if ($callAccess->update() === false) {
                            Yii::error(
                                VarDumper::dumpAsString($callAccess->errors),
                                'Call:afterSave:CallUserAccess:update'
                            );
                        } else {
                            if ($sendWarmTransferMissedNotification) {
                                $message = 'Missed Call (Id: ' . Purifier::createCallShortLink($this) . ')  from ';
                                if ($this->c_lead_id && $this->cLead) {
                                    $message .= $this->cLead->client ? $this->cLead->client->getFullName() : '';
                                    $message .= '<br> Lead (Id: ' . Purifier::createLeadShortLink($this->cLead) . ')';
                                    $message .= $this->cLead->project ? '<br> ' . $this->cLead->project->name : '';
                                }
                                if ($this->c_case_id && $this->cCase) {
                                    $message .= $this->cCase->client ? $this->cCase->client->getFullName() : '';
                                    $message .= '<br> Case (Id: ' . Purifier::createCaseShortLink($this->cCase) . ')';
                                    $message .= $this->cCase->project ? '<br> ' . $this->cCase->project->name : '';
                                }

                                if (
                                    $ntf = Notifications::create(
                                        $callAccess->cua_user_id,
                                        'Missed Call (' . self::SOURCE_LIST[self::SOURCE_TRANSFER_CALL] . ')',
                                        $message,
                                        Notifications::TYPE_WARNING,
                                        true
                                    )
                                ) {
                                    $dataNotification = (Yii::$app->params['settings']['notification_web_socket']) ? NotificationMessage::add($ntf) : [];
                                    Notifications::publish('getNewNotification', ['user_id' => $callAccess->cua_user_id], $dataNotification);
                                }
                            }
                        }
                        Notifications::publish(RemoveIncomingRequestMessage::COMMAND, ['user_id' => $callAccess->cua_user_id], RemoveIncomingRequestMessage::create($this->c_call_sid));
                    }
                }

                if ((int)$this->c_source_type_id !== self::SOURCE_CONFERENCE_CALL && $this->isIn()) {
                    /** @var Call $parent */
                    if (!$this->c_parent_id || (($parent = self::find()->andWhere(['c_id' => $this->c_parent_id])->one()) && $parent->isOut())) {
//                        $isCallUserAccepted = CallUserAccess::find()->where([
//                            'cua_status_id' => CallUserAccess::STATUS_TYPE_ACCEPT,
//                            'cua_call_id' => $this->c_id
//                        ])->exists();
//
//                        if (!$isCallUserAccepted && !$this->isDeclined()) {
//                            $this->c_status_id = self::STATUS_NO_ANSWER;
//                            self::updateAll(['c_status_id' => self::STATUS_NO_ANSWER], ['c_id' => $this->c_id]);
//                        }
                        if (!$this->isDeclined()) {
//                            /** @var Call $lastChild */
//                            $lastChild = self::find()->andWhere(['c_parent_id' => $this->c_id])->orderBy(['c_id' => SORT_DESC])->limit(1)->one();
//                            if (
//                                $lastChild === null
//                                || (
//                                    $lastChild
//                                    && $lastChild->c_created_user_id != null
//                                    && (
//                                        $this->c_created_user_id == null || $this->c_created_user_id != $lastChild->c_created_user_id
//                                    )
//                                )
//                            ) {
//                                $this->c_status_id = self::STATUS_NO_ANSWER;
//                                self::updateAll(['c_status_id' => self::STATUS_NO_ANSWER], ['c_id' => $this->c_id]);
//                            }

                            if (!$this->currentParticipant || ($this->currentParticipant && !$this->currentParticipant->isUser())) {
                                $cuaExists = CallUserAccess::find()->andWhere([
                                    'cua_call_id' => $this->c_id, 'cua_status_id' => CallUserAccess::STATUS_TYPE_ACCEPT
                                ])->andWhere(['>=', 'cua_updated_dt', $this->c_queue_start_dt])->exists();
                                if (!$cuaExists && !$this->c_conference_id) {
                                    $this->c_status_id = self::STATUS_NO_ANSWER;
                                    self::updateAll(['c_status_id' => self::STATUS_NO_ANSWER], ['c_id' => $this->c_id]);
                                }
                            }
                        }
                    }
                }


                //|| ($this->isStatusCompleted() && !$this->c_parent_id && !CallUserAccess::find()->where(['cua_status_id' => CallUserAccess::STATUS_TYPE_ACCEPT, 'cua_call_id' => $this->c_id])->exists()))
            }


//            if ($this->c_case_id && $isChangedStatus && $this->isIn() && $this->isStatusInProgress()) {
//                if ($this->c_created_user_id && $this->cCase && $this->c_created_user_id !== $this->cCase->cs_user_id) {
//                    try {
//                        $casesManageService = Yii::createObject(CasesManageService::class);
//                        $casesManageService->take($this->c_case_id, $this->c_created_user_id, null);
//                    } catch (\Throwable $exception) {
//                        Yii::error(VarDumper::dumpAsString($exception), 'Call:afterSave:CasesManageService:Case:Take');
//                    }
//                }
//            }

            //Yii::info(VarDumper::dumpAsString($this->attributes), 'info\Call:afterSave');
        }

        if (
            $this->c_created_user_id
            && ($this->c_lead_id || $this->c_case_id)
            && $isChangedStatusFromEmptyInclude
            && in_array($changedAttributes['c_status_id'], [self::STATUS_RINGING, null], true)
            && $this->isStatusInProgress()
            && $this->isIn()
        ) {
            $host = \Yii::$app->params['url'] ?? '';

            if ($this->c_dep_id && ($departmentParams = $this->cDep->getParams())) {
                if ($this->c_lead_id && $departmentParams->object->type->isLead()) {
                    $lead = $this->cLead;

                    if ($lead && !$lead->employee_id && $lead->isPending()) {
                        //Yii::info(VarDumper::dumpAsString(['changedAttributes' => $changedAttributes, 'Call' => $this->attributes, 'Lead' => $lead->attributes]), 'info\Call:Lead:afterSave');
                        try {
                            $lead->answered();
                            $lead->processing(
                                $this->c_created_user_id,
                                null,
                                LeadFlow::DESCRIPTION_CALL_AUTO_CREATED_LEAD
                            );
                            $leadRepository->save($lead);

                            $leadUserConversionService = Yii::createObject(LeadUserConversionService::class);
                            $leadUserConversionService->addAutomate(
                                $lead->id,
                                $this->c_created_user_id,
                                LeadUserConversionDictionary::DESCRIPTION_CALL_AUTO_TAKE,
                                $this->c_created_user_id
                            );

                            $qCallService->remove($lead->id);

                            if (
                                $ntf = Notifications::create(
                                    $lead->employee_id,
                                    'AutoCreated new Lead (' . $lead->id . ')',
                                    'A new Lead (Id: ' . Purifier::createLeadShortLink($lead) . ') has been created for you. Call Id: ' . $this->c_id,
                                    Notifications::TYPE_SUCCESS,
                                    true
                                )
                            ) {
                                $dataNotification = (Yii::$app->params['settings']['notification_web_socket']) ? NotificationMessage::add($ntf) : [];
                                Notifications::publish(
                                    'getNewNotification',
                                    ['user_id' => $lead->employee_id],
                                    $dataNotification
                                );
                            }
//                            $userListSocketNotification[$lead->employee_id] = $lead->employee_id;
                            // Notifications::publish('openUrl', ['user_id' => $lead->employee_id], ['url' => $host . '/lead/view/' . $lead->gid], false);

                            $pubChannel = UserConnection::getLastUserChannel($lead->employee_id);
                            Notifications::pub(
                                [$pubChannel],
                                'openUrl',
                                ['url' => $host . '/lead/view/' . $lead->gid]
                            );
                        } catch (\Throwable $e) {
                            Yii::error(
                                'CallId: ' . $this->c_id . ' LeadId: ' . $lead->id . ' Message: ' . $e->getMessage(),
                                'Call:afterSave:Lead:Answered:Processing'
                            );
                        }
                    }
                    /** @fflag FFlag::FF_KEY_BEQ_ENABLE, Business Extra Queue enable */
                    if (\Yii::$app->featureFlag->isEnable(FFlag::FF_KEY_BEQ_ENABLE) && $lead && $lead->employee_id && $lead->isProcessing() && $lead->isBusinessType()) {
                        LeadBusinessExtraQueueService::addLeadBusinessExtraQueueRemoverJob(
                            $lead->id,
                            LeadBusinessExtraQueueLogStatus::REASON_INCOMING_CALL
                        );
                    }
                }


                if ($this->c_case_id && $departmentParams->object->type->isCase()) {
                    $case = $this->cCase;

                    if ($case && !$case->cs_user_id && $case->isPending()) {
                        try {
                            $casesManageService = Yii::createObject(CasesManageService::class);
                            $casesManageService->callAutoTake(
                                $this->c_case_id,
                                $this->c_created_user_id,
                                null,
                                'Call auto take'
                            );

//                            $caseRepo = Yii::createObject(CasesRepository::class);
//                            $case->processing((int)$this->c_created_user_id, null);
//                            $caseRepo->save($case);

                            if (
                                $ntf = Notifications::create(
                                    $this->c_created_user_id,
                                    'AutoCreated new Case (' . $case->cs_id . ')',
                                    'A new Case (Id: ' . Purifier::createCaseShortLink($case) . ') has been created for you. Call Id: ' . $this->c_id,
                                    Notifications::TYPE_SUCCESS,
                                    true
                                )
                            ) {
                                $dataNotification = (Yii::$app->params['settings']['notification_web_socket']) ? NotificationMessage::add($ntf) : [];
                                Notifications::publish(
                                    'getNewNotification',
                                    ['user_id' => $this->c_created_user_id],
                                    $dataNotification
                                );
                            }

//                            $userListSocketNotification[$case->cs_user_id] = $case->cs_user_id;
                            // Notifications::publish('openUrl', ['user_id' => $case->cs_user_id], ['url' => $host . '/cases/view/' . $case->cs_gid], false);
                            $pubChannel = UserConnection::getLastUserChannel($this->c_created_user_id);
                            Notifications::pub(
                                [$pubChannel],
                                'openUrl',
                                ['url' => $host . '/cases/view/' . $case->cs_gid]
                            );
                        } catch (\DomainException $e) {
                        } catch (\Throwable $e) {
                            Yii::error($e->getMessage(), 'Call:afterSave:Case:update');
                        }
                    }
                }
            }
        }

        if (($insert || $isChangedStatus) && $this->isIn() && ($this->isStatusNoAnswer() || $this->isStatusBusy())) {
//                    $callAcceptExist = CallUserAccess::find()->where(['cua_status_id' => CallUserAccess::STATUS_TYPE_ACCEPT, 'cua_call_id' => $this->c_id])->exists();
//                    if (!$callAcceptExist) {

            $userListNotifications = [];

            if ($this->c_created_user_id) {
                $userListNotifications[$this->c_created_user_id] = $this->c_created_user_id;

                $dataNotification = (Yii::$app->params['settings']['notification_web_socket']) ? MissedCallMessage::add($this) : [];
                Notifications::publish(MissedCallMessage::COMMAND, ['user_id' => $this->c_created_user_id], $dataNotification);
            }

            if ($changedAttributes['c_status_id'] !== self::STATUS_HOLD) {
                if ($this->c_lead_id && $this->cLead && $this->cLead->employee_id) {
                    $userListNotifications[$this->cLead->employee_id] = $this->cLead->employee_id;
                }

                if ($this->c_case_id && $this->cCase && $this->cCase->cs_user_id) {
                    $userListNotifications[$this->cCase->cs_user_id] = $this->cCase->cs_user_id;
                }
            }

            if ($userListNotifications) {
                //$from = PhoneFormatter::getPhoneOrNickname($this->c_from);
                //$to = PhoneFormatter::getPhoneOrNickname($this->c_to);
                $msgPart = '';
                if ($this->c_source_type_id != self::SOURCE_DIRECT_CALL) {
                    $msgPart = 'Queued ';
                }

                $holdMessage = $changedAttributes['c_status_id'] === self::STATUS_HOLD ? ' Hold' : '';
                $title = 'Missed' . $holdMessage . ' Call (' . $this->getSourceName() . ')';
                $message = 'Missed ' . $msgPart . $holdMessage . 'Call (Id: ' . Purifier::createCallShortLink($this) . ')  from ';
                if ($this->c_lead_id && $this->cLead) {
                    $message .= $this->cLead->client ? $this->cLead->client->getFullName() : '';
                    $message .= '<br> Lead (Id: ' . Purifier::createLeadShortLink($this->cLead) . ')';
                    $message .= $this->cLead->project ? '<br> ' . $this->cLead->project->name : '';
                }

                if ($this->c_case_id && $this->cCase) {
                    $message .= $this->cCase->client ? $this->cCase->client->getFullName() : '';
                    $message .= '<br> Case (Id: ' . Purifier::createCaseShortLink($this->cCase) . ')';
                    $message .= $this->cCase->project ? '<br> ' . $this->cCase->project->name : '';
                }

                foreach ($userListNotifications as $userId) {
                    if ($ntf = Notifications::create($userId, $title, $message, Notifications::TYPE_WARNING, true)) {
                        $dataNotification = (Yii::$app->params['settings']['notification_web_socket']) ? NotificationMessage::add($ntf) : [];
                        Notifications::publish('getNewNotification', ['user_id' => $userId], $dataNotification);
                    }
                    // Notifications::socket($userId, null, 'getNewNotification', [], true);
//                    $userListSocketNotification[$userId] = $userId;
                }
            }

            if ($this->c_case_id) {
                (Yii::createObject(CasesManageService::class))->needAction($this->c_case_id);
            }



            //}
        }

        if (
            ($insert || $isChangedStatus)
            && $this->isIn()
            && ($this->isStatusCanceled() || $this->isStatusNoAnswer() || $this->isStatusBusy())
            && ($lead = $this->cLead)
        ) {
            if ($lead->isFollowUp()) {
                try {
                    $lead->pending($lead->employee_id, null, 'missed call');
                    $leadRepository->save($lead);
                    $qCallService->remove($lead->id);
                    $qCallService->create(
                        $lead->id,
                        new Config($lead->status, $lead->getCountOutCallsLastFlow()),
                        new FindWeightParams($lead->project_id, $lead->status),
                        $lead->offset_gmt,
                        new FindPhoneParams($lead->project_id, $lead->l_dep_id)
                    );
                } catch (\Throwable $e) {
                    Yii::error($e->getMessage(), 'Call:afterSave:Lead:pending');
                }
            } elseif ($lead->isPending()) {
                try {
                    $qCallService->resetAttempts($lead);
                    $qCallService->remove($lead->id);
                    $qCallService->create(
                        $lead->id,
                        new Config($lead->status, $lead->getCountOutCallsLastFlow()),
                        new FindWeightParams($lead->project_id, $lead->status),
                        $lead->offset_gmt,
                        new FindPhoneParams($lead->project_id, $lead->l_dep_id)
                    );
                } catch (\Throwable $e) {
                    Yii::error($e->getMessage(), 'Call:afterSave:Lead:resetAttempts');
                }
            }
        }

        if ($this->c_lead_id && ($lead = $this->cLead)) {
            if (($isChangedStatus || $insert) && $this->isIn() && $this->isEnded()) {
                if ($lead->isCallQueue()) {
                    try {
                        $lead->callReady();
                        $leadRepository->save($lead);
                    } catch (\Throwable $e) {
                        Yii::error('CallId: ' . $this->c_id . ' LeadId: ' . $lead->id . ' Message: ' . $e->getMessage(), 'Call:AfterSave:Lead:isCallQueue:callReady');
                    }
                }
            }

            if ($this->isOut()) {
                $this->cLead->updateLastAction(LeadPoorProcessingLogStatus::REASON_CALL);
            }
        }

        if ($this->cCase) {
            $this->cCase->updateLastAction();
            if ($this->c_parent_id && $this->isOut() && $this->isEnded() && $isChangedStatus) {
                $this->cCase->addEventLog(null, $this->getCallTypeName() . ' call finished in status: ' . $this->getStatusName() . '.' . ($this->c_created_user_id ?  (' By: ' . $this->cCreatedUser->username) : ''));
            }
        }


//        if (
//            $this->c_created_user_id && ($insert || $isChangedStatusFromEmptyInclude)
//            && (!($this->isIn() && $this->isStatusQueue()))
//            && (!($this->isIn() && $this->isStatusDelay()))
//            && (!$this->isInternal() || $this->isEnded())
////            && (!($this->isIn() && $this->isStatusRinging() && $this->isInternal()))
//            && (!($this->isOut() && $this->isChild()))
//            && (!($this->isIn() && $this->isChild() && $this->isStatusRinging()))
//        ) {
//            if (
//                $this->isEnded()
//                || !$this->currentParticipant
//                || ($this->currentParticipant && ($this->currentParticipant->isAgent() || $this->currentParticipant->isUser()))
//            ) {
//                $message = (new CallUpdateMessage())->create($this, $isChangedStatus, $this->c_created_user_id);
//
//                Notifications::publish('callUpdate', ['user_id' => $this->c_created_user_id], $message);
//                if ($this->isActiveStatus()) {
//                    UserStatus::isOnCallOn($this->c_created_user_id);
//                } else {
//                    UserStatus::isOnCallOff($this->c_created_user_id);
//                }
//            }
//        }

        if ($this->c_created_user_id && ($insert || $isChangedStatusFromEmptyInclude) && $this->creatorTypeIsAgent()) {
            if ($this->isStatusRinging() && !$this->isOut()) {
            } else {
                $message = (new CallUpdateMessage())->create($this, $isChangedStatus, $this->c_created_user_id);
                Notifications::publish('callUpdate', ['user_id' => $this->c_created_user_id], $message);
            }

            if ($this->isActiveStatus()) {
                UserStatus::isOnCallOn($this->c_created_user_id);
            } else {
                UserStatus::isOnCallOff($this->c_created_user_id);
            }
        }

        if ($this->c_created_user_id && $this->getDataCreatorType()->isUser()) {
            if ($this->isOut() && ($insert || $isChangedStatus) && $this->isStatusRinging()) {
                $message = (new CallUpdateMessage())->create($this, $isChangedStatus, $this->c_created_user_id);
                Notifications::publish('callUpdate', ['user_id' => $this->c_created_user_id], $message);
                UserStatus::isOnCallOn($this->c_created_user_id);
            }

            if ($this->isIn() && ($insert || $isChangedStatus) && $this->c_parent_id && $this->isStatusRinging()) {
                $internalParent = $this->cParent;
                $internalParent->c_status_id = self::STATUS_RINGING;
                $message = (new CallUpdateMessage())->create($internalParent, $isChangedStatus, $this->c_created_user_id);
                Notifications::publish('callUpdate', ['user_id' => $internalParent->c_created_user_id], $message);
                if ($internalParent->isActiveStatus()) {
                    UserStatus::isOnCallOn($internalParent->c_created_user_id);
                }
            }

            if ($this->isIn() && ($insert || $isChangedStatus) && $this->c_parent_id && $this->isStatusInProgress()) {
                $internalParent = $this->cParent;
                $parentMessage = (new CallUpdateMessage())->create($internalParent, $isChangedStatus, $this->c_created_user_id);
                Notifications::publish('callUpdate', ['user_id' => $internalParent->c_created_user_id], $parentMessage);
                if ($internalParent->isActiveStatus()) {
                    UserStatus::isOnCallOn($internalParent->c_created_user_id);
                }

                $message = (new CallUpdateMessage())->create($this, $isChangedStatus, $this->c_created_user_id);
                Notifications::publish('callUpdate', ['user_id' => $this->c_created_user_id], $message);
                if ($this->isActiveStatus()) {
                    UserStatus::isOnCallOn($this->c_created_user_id);
                }
            }

            if (($insert || $isChangedStatus) && !$this->isActiveStatus()) {
                $message = (new CallUpdateMessage())->create($this, $isChangedStatus, $this->c_created_user_id);
                Notifications::publish('callUpdate', ['user_id' => $this->c_created_user_id], $message);
                UserStatus::isOnCallOff($this->c_created_user_id);
            }
        }

        if ($isChangedStatus) {
            NativeEventDispatcher::recordEvent(CallEvents::class, CallEvents::CHANGE_STATUS, [CallEvents::class, 'updateUserStatus'], ['call' => $this, 'changedAttributes' => $changedAttributes]);
            NativeEventDispatcher::trigger(CallEvents::class, CallEvents::CHANGE_STATUS);
        }

//        if (($this->c_lead_id || $this->c_case_id) && !$this->isJoin()) {
//            //Notifications::socket(null, $this->c_lead_id, 'updateCommunication', ['lead_id' => $this->c_lead_id, 'status_id' => $this->c_status_id, 'status' => $this->getStatusName()], true);
//
//            $socketParams = [];
//            if ($this->c_lead_id) {
//                $socketParams['lead_id'] = $this->c_lead_id;
//            }
//
//            if ($this->c_case_id) {
//                $socketParams['case_id'] = $this->c_case_id;
//            }
//
//            Notifications::publish('updateCommunication', $socketParams, ['lead_id' => $this->c_lead_id, 'case_id' => $this->c_case_id, 'status_id' => $this->c_status_id, 'status' => $this->getStatusName()]);
//        }

//        if ($userListSocketNotification) {
//            foreach ($userListSocketNotification as $userId) {
//                Notifications::sendSocket('getNewNotification', ['user_id' => $userId]);
//            }
//            unset($userListSocketNotification);
//        }

        Notifications::pingUserMap();

        $isChangedTwStatus = array_key_exists('c_call_status', $changedAttributes);

        $logEnable = Yii::$app->params['settings']['call_log_enable'] ?? false;
        if ($logEnable) {
            $isChangedDuration = array_key_exists('c_call_duration', $changedAttributes);
            if (
                Yii::$app->id === 'app-webapi'
                && $this->isTwFinishStatus()
                && ($insert || $isChangedTwStatus || $isChangedDuration)
            ) {
                (Yii::createObject(CallLogTransferService::class))->transfer($this);
                if ($this->c_client_id && $this->isOut() && $this->getDataCreatorType()->isClient()) {
                    $callOutEndedJob = new CallOutEndedJob($this->c_client_id, $this->c_id);
                    Yii::$app->queue_job->priority(10)->push($callOutEndedJob);
                }

                if ($this->c_client_id && ($this->isOut() || $this->isIn()) && ($this->getDataCreatorType()->isClient() || $this->getDataCreatorType()->isAgent())) {
                    if (($lead = $this->cLead) && (new LeadTaskListService($lead))->isProcessAllowed()) {
                        $job = new UserTaskCompletionJob(
                            TargetObject::TARGET_OBJ_LEAD,
                            $lead->id,
                            TaskObject::OBJ_CALL,
                            $this->c_id,
                            $lead->employee_id
                        );
                        Yii::$app->queue_job->push($job);
                    }
                }
            }
//            if (($insert || $isChangedTwStatus) && $this->isTwFinishStatus()) {
//                if (Yii::$app->id === 'app-webapi') {
//                    Yii::info($this->getAttributes(), 'info\DebugAfterSave_WebApi');
//                    (Yii::createObject(CallLogTransferService::class))->transfer($this);
//                } else {
//                    Yii::info($this->getAttributes(), 'info\DebugAfterSave_Other');
//                }
//            }
        }

        if ($isChangedTwStatus && $this->isCompletedTw()) {
            $createJob = (bool)(Yii::$app->params['settings']['call_price_job'] ?? false);
            if ($createJob) {
                $delayJob = 60;
                $job = new CallPriceJob();
                $job->callSids = [$this->c_call_sid];
                $job->delayJob = $delayJob;
                Yii::$app->queue_job->delay($delayJob)->priority(10)->push($job);
            }
        }

        $this->sendFrontendData();
    }

    public static function getQueueName(Call $call): string
    {
        if ($call->isStatusInProgress()) {
            return self::QUEUE_IN_PROGRESS;
        }
        if ($call->isHold()) {
            return self::QUEUE_HOLD;
        }
        if ($call->c_source_type_id === self::SOURCE_GENERAL_LINE) {
            return self::QUEUE_GENERAL;
        }
        if ($call->c_source_type_id === self::SOURCE_DIRECT_CALL || $call->c_source_type_id === self::SOURCE_INTERNAL) {
            return self::QUEUE_DIRECT;
        }
        return '';
    }

    public function isTwFinishStatus(): bool
    {
        return $this->isCompletedTw() || $this->isBusyTw() || $this->isNoAnswerTw() || $this->isFailedTw() || $this->isCanceledTw();
    }

    public function isFinishStatus(): bool
    {
        return $this->isStatusCompleted() || $this->isStatusBusy() || $this->isStatusNoAnswer() || $this->isStatusFailed() || $this->isStatusCanceled() || $this->isStatusDeclined();
    }

    /**
     * @param Call $call
     * @param int $user_id
     * @param string $deviceIdentity
     * @return bool
     */
    public static function applyCallToAgent(Call $call, int $user_id, string $deviceIdentity): bool
    {
        try {
            if ($call) {
                if ($call->isStatusQueue()) {
                } else {
                    \Yii::warning('Error: Call (' . $call->getStatusName() . ', ' . $call->c_call_status . ') not in status QUEUE: ' . $call->c_id . ',  User: ' . $user_id, 'Call:applyCallToAgent:callRedirect');
                    return false;
                }

                $call->setStatusDelay();

                if ($call->c_created_user_id && (int) $call->c_created_user_id !== $user_id) {
                    $call->c_source_type_id = self::SOURCE_REDIRECT_CALL;

                    $user = Employee::findOne($user_id);

                    $message = 'Missed Queued Call (Id: ' . Purifier::createCallShortLink($call) . ')  from ';
                    if ($call->c_lead_id && $call->cLead) {
                        $message .= $call->cLead->client ? $call->cLead->client->getFullName() : '';
                        $message .= '<br> Lead (Id: ' . Purifier::createLeadShortLink($call->cLead) . ')';
                        $message .= $call->cLead->project ? '<br> ' . $call->cLead->project->name : '';
                        $message .= $call->cLead->lDep ? ' / ' . $call->cLead->lDep->dep_name : '';
                    }
                    if ($call->c_case_id && $call->cCase) {
                        $message .= $call->cCase->client ? $call->cCase->client->getFullName() : '';
                        $message .= '<br> Case (Id: ' . Purifier::createCaseShortLink($call->cCase) . ')';
                        $message .= $call->cCase->project ? '<br> ' . $call->cCase->project->name : '';
                        $message .= $call->cCase->department ? ' / ' . $call->cCase->department->dep_name : '';
                    }

                    $message .= '<br> Taken by Agent: ' . ($user ? Html::encode($user->username) : '-');

                    if (
                        $ntf = Notifications::create(
                            $call->c_created_user_id,
                            'Missed Queued Call (' . $call->getSourceName() . ')',
                            $message,
                            Notifications::TYPE_WARNING,
                            true
                        )
                    ) {
                        // Notifications::socket($call->c_created_user_id, null, 'getNewNotification', [], true);
                        $dataNotification = (Yii::$app->params['settings']['notification_web_socket']) ? NotificationMessage::add($ntf) : [];
                        Notifications::publish('getNewNotification', ['user_id' => $call->c_created_user_id], $dataNotification);
                    }
                }

                $call->c_created_user_id = $user_id;

                $callUserAccessAny = CallUserAccess::find()->where(['cua_status_id' => [CallUserAccess::STATUS_TYPE_PENDING, CallUserAccess::STATUS_TYPE_WARM_TRANSFER], 'cua_call_id' => $call->c_id])->andWhere(['!=', 'cua_user_id', $call->c_created_user_id])->all();
                if ($callUserAccessAny) {
                    foreach ($callUserAccessAny as $callAccess) {
                        $callAccess->noAnsweredCall();
                        if ($callAccess->update() === false) {
                            Yii::error(VarDumper::dumpAsString($callAccess->errors), 'Call:applyCallToAgent:CallUserAccess:save');
                        }
                        Notifications::publish(RemoveIncomingRequestMessage::COMMAND, ['user_id' => $callAccess->cua_user_id], RemoveIncomingRequestMessage::create($call->c_call_sid));
                    }
                }

                $isDisabledRecord = (RecordManager::acceptCall(
                    $user_id,
                    $call->c_project_id,
                    $call->c_dep_id,
                    null,
                    $call->c_client_id
                ))->isDisabledRecord();

                if ($isDisabledRecord) {
                    $call->recordingDisable();
                } else {
                    $call->recordingEnable();
                }

                if ($call->update() === false) {
                    Yii::error(VarDumper::dumpAsString(['call' => $call->getAttributes(), 'error' => $call->getErrors()]), 'Call:applyCallToAgent:call:update');
                } else {
                    $delay = abs((int)(Yii::$app->params['settings']['call_accept_check_conference_status_seconds'] ?? 0));
                    if ($delay) {
                        $checkJob = new CheckClientCallJoinToConferenceJob();
                        $checkJob->callId = $call->c_id;
                        $checkJob->dateTime = date('Y-m-d H:i:s');
                        $checkJob->delayJob = $delay;
                        Yii::$app->queue_job->delay($delay)->push($checkJob);
                    }
                }

                if (!$call->isConferenceType()) {
                    $call->setConferenceType();
                    $call->update();
                }

                $res = \Yii::$app->comms->acceptConferenceCall(
                    $call->c_id,
                    $call->c_call_sid,
                    $deviceIdentity,
                    $call->c_from,
                    $user_id,
                    $call->isRecordingDisable(),
                    $call->getDataPhoneListId(),
                    $call->c_to,
                    FriendlyName::nextWithSid($call->c_call_sid),
                    $call->c_project_id ? $call->cProject->name : '',
                    $call->getSourceName(),
                    $call->getCallTypeName()
                );

                if ($res) {
                    $isError = (bool)($res['error'] ?? true);
                    if ($isError) {
                        $call->c_call_status = self::TW_STATUS_CANCELED;
                        $call->setStatusByTwilioStatus($call->c_call_status);
                        $call->c_created_user_id = null;
                        $call->update();

                        if (!empty($res['message']) && $res['message'] === 'Call status is Completed') {
                            Notifications::publish('showNotification', ['user_id' => $user_id], [
                                'data' => [
                                    'title' => 'Accept call',
                                    'message' => 'The other side hung up',
                                    'type' => 'warning',
                                ]
                            ]);
                        }
                        return false;
                    }
                    return true;
                }

                \Yii::warning('Error: ' . VarDumper::dumpAsString($res), 'Call:applyCallToAgent:callRedirect');
            } else {
                \Yii::warning('Error: Not found Call' . VarDumper::dumpAsString($call), 'Call:applyCallToAgent:callRedirect');
            }
        } catch (\Throwable $e) {
            \Yii::error($e, 'Call:applyCallToAgent');
        }
        return false;
    }

    /**
     * @param Call $call
     * @param int $user_id
     * @param string $deviceIdentity
     * @return bool
     */
    public static function applyWarmTransferCallToAgent(Call $call, int $user_id, string $deviceIdentity): bool
    {
        try {
            if (!$call->isStatusDelay() && !$call->isStatusInProgress() && !$call->isHold()) {
                throw new \DomainException('Call is Not in status Delay or In-progress or Hold');
            }

//                $call->c_source_type_id = self::SOURCE_REDIRECT_CALL;

//            if ($call->c_created_user_id) {
//                $user = Employee::findOne($user_id);
//                $message = 'Warm transfer accepted successfully.';
//                if (
//                    $ntf = Notifications::create(
//                        $call->c_created_user_id,
//                        'Warm transfer',
//                        $message,
//                        Notifications::TYPE_SUCCESS,
//                        true
//                    )
//                ) {
//                    $dataNotification = (Yii::$app->params['settings']['notification_web_socket']) ? NotificationMessage::add($ntf) : [];
//                    Notifications::publish('getNewNotification', ['user_id' => $call->c_created_user_id], $dataNotification);
//                }
//            }

//                $call->c_created_user_id = $user_id;

            $callUserAccessAny = CallUserAccess::find()->where([
                'cua_status_id' => [
                    CallUserAccess::STATUS_TYPE_PENDING,
                    CallUserAccess::STATUS_TYPE_WARM_TRANSFER
                ],
                'cua_call_id' => $call->c_id
            ])->all();
            if ($callUserAccessAny) {
                foreach ($callUserAccessAny as $callAccess) {
                    $callAccess->noAnsweredCall();
                    if ($callAccess->update() === false) {
                        Yii::error(VarDumper::dumpAsString($callAccess->errors), 'Call:applyWarmTransferCallToAgent');
                    }
                    Notifications::publish(RemoveIncomingRequestMessage::COMMAND, ['user_id' => $callAccess->cua_user_id], RemoveIncomingRequestMessage::create($call->c_call_sid));
                }
            }

            $isDisabledRecord = (RecordManager::acceptCall(
                $user_id,
                $call->c_project_id,
                $call->c_dep_id,
                null,
                $call->c_client_id
            ))->isDisabledRecord();

            $oldRecordingDisabled = $call->c_recording_disabled;

            if ($isDisabledRecord) {
                $call->recordingDisable();
            } else {
                $call->recordingEnable();
            }

            if ($call->update() === false) {
                Yii::error(VarDumper::dumpAsString(['call' => $call->getAttributes(), 'error' => $call->getErrors()]), 'Call:applyWarmTransferCallToAgent:call:update');
            } else {
//                $delay = abs((int)(Yii::$app->params['settings']['call_accept_check_conference_status_seconds'] ?? 0));
//                if ($delay) {
//                    $checkJob = new CheckClientCallJoinToConferenceJob();
//                    $checkJob->callId = $call->c_id;
//                    $checkJob->dateTime = date('Y-m-d H:i:s');
//                    Yii::$app->queue_job->delay($delay)->push($checkJob);
//                }
            }

            $departmentId = null;
            $userDepartment = UserProjectParams::find()->select(['upp_dep_id'])->andWhere(['IS NOT', 'upp_dep_id', null])->byUserId($user_id)->byProject($call->c_project_id)->asArray()->one();
            if ($userDepartment) {
                $departmentId = $userDepartment['upp_dep_id'];
            }

            $res = \Yii::$app->comms->acceptWarmTransferCall(
                $call->c_id,
                $call->c_call_sid,
                $deviceIdentity,
                $call->c_from,
                $user_id,
                $call->isRecordingDisable(),
                $call->getDataPhoneListId(),
                $call->c_to,
                FriendlyName::nextWithSid($call->c_call_sid),
                $departmentId,
                $call->c_created_user_id,
                $call->c_group_id,
                $call->c_project_id ? $call->cProject->name : '',
                $call->getSourceName(),
                $call->getCallTypeName()
            );

            $isError = (bool)($res['error'] ?? true);
            if ($isError) {
                if ($oldRecordingDisabled) {
                    $call->recordingDisable();
                } else {
                    $call->recordingEnable();
                }
                $call->update();

                if (!empty($res['message']) && $res['message'] === 'Call status is Completed') {
                    Notifications::publish('showNotification', ['user_id' => $user_id], [
                        'data' => [
                            'title' => 'Accept call',
                            'message' => 'The other side hung up',
                            'type' => 'warning',
                        ]
                    ]);
                }
                return false;
            }
            return true;
        } catch (\Throwable $e) {
            \Yii::error($e, 'Call:applyCallToAgent');
        }
        return false;
    }

    /**
     * @param Call $call
     * @param int $user_id
     * @return bool
     */
    public static function applyCallToAgentAccess(Call $call, int $user_id): bool
    {
        try {
            $callUserAccess = CallUserAccess::find()->where(['cua_user_id' => $user_id, 'cua_call_id' => $call->c_id])->one();
            if (!$callUserAccess) {
                $callUserAccess = new CallUserAccess();
                $callUserAccess->cua_call_id = $call->c_id;
                $callUserAccess->cua_user_id = $user_id;
                $callUserAccess->acceptPending();
                $callUserAccess->cua_priority = $call->getDataPriority();
            } else {
                $callUserAccess->acceptPending();
                $callUserAccess->cua_created_dt = date("Y-m-d H:i:s");
                $callUserAccess->cua_priority = $call->getDataPriority();
            }
            if ($callUserAccess->save()) {
                return true;
            }
            $errors = $callUserAccess->getErrors();
            foreach ($errors as $error) {
                if (strpos($error[0], 'has already been taken') !== false) {
                    return true;
                }
            }
            Yii::error(VarDumper::dumpAsString($errors), 'Call:applyCallToAgentAccess:callUserAccess:save');
        } catch (\Throwable $e) {
            if (DuplicateExceptionChecker::isDuplicate($e->getMessage())) {
                return true;
            }
            \Yii::error($e, 'Call:applyCallToAgentAccess');
        }
        return false;
    }

    public static function applyCallToAgentAccessWarmTransfer(Call $call, int $user_id): bool
    {
        try {
            $callUserAccess = CallUserAccess::find()->where(['cua_user_id' => $user_id, 'cua_call_id' => $call->c_id])->one();
            if (!$callUserAccess) {
                $callUserAccess = new CallUserAccess();
                $callUserAccess->cua_call_id = $call->c_id;
                $callUserAccess->cua_user_id = $user_id;
                $callUserAccess->warmTransfer();
                $callUserAccess->cua_priority = $call->getDataPriority();
            } else {
                $callUserAccess->warmTransfer();
                $callUserAccess->cua_created_dt = date("Y-m-d H:i:s");
                $callUserAccess->cua_priority = $call->getDataPriority();
            }

            if (!$callUserAccess->save()) {
                Yii::error(VarDumper::dumpAsString($callUserAccess->errors), 'Call:applyCallToAgentAccessWarmTransfer');
            } else {
                return true;
            }
        } catch (\Throwable $e) {
            \Yii::error($e, 'Call:applyCallToAgentAccessWarmTransfer');
        }
        return false;
    }



    /**
     * @param string $startDate
     * @param string $endDate
     * @param string $groupingBy
     * @param int $callType
     * @return array
     * @throws \Exception
     */
    public static function getCallStats(string $startDate, string $endDate, ?string $groupingBy, int $callType): array
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
                    $hoursRange = ChartTools::getHoursRange($startDate, $endDate . ' 23:59:59', $step = '+1 hour', $format = 'H:i:s');
                } else {
                    $hoursRange = ChartTools::getHoursRange($startDate, $endDate . ' 23:59:59', $step = '+1 hour', $format = 'Y-m-d H:i:s');
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

        if ($callType == 0) {
            $calls = self::find()->select(['c_call_status', 'c_updated_dt', 'c_call_duration', 'c_price'])
                ->where(['c_call_status' => ['completed', 'busy', 'no-answer', 'canceled']])
                ->andWhere(['between', 'c_updated_dt', $sDate, $eDate])->all();
        } else {
            $calls = self::find()->select(['c_call_status', 'c_updated_dt', 'c_call_duration', 'c_price'])
                ->where(['c_call_status' => ['completed', 'busy', 'no-answer', 'canceled']])
                ->andWhere(['between', 'c_updated_dt', $sDate, $eDate])
                ->andWhere(['=', 'c_call_type_id', $callType])->all();
        }

        $callStats = [];
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

        $completed = $noAnswer = $busy = $canceled = 0;
        $cc_Duration = $cc_TotalPrice = 0;
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
            foreach ($calls as $callItem) {
                $callUpdatedTime = date($dateFormat, strtotime($callItem->c_updated_dt));
                if ($callUpdatedTime >= $timeSignature && $callUpdatedTime <= $EndPoint) {
                    switch ($callItem->c_call_status) {
                        case self::TW_STATUS_COMPLETED:
                            $completed++;
                            $cc_Duration += $callItem->c_call_duration;
                            $cc_TotalPrice += $callItem->c_price;
                            break;
                        case self::TW_STATUS_NO_ANSWER:
                            $noAnswer++;
                            break;
                        case self::TW_STATUS_BUSY:
                            $busy++;
                            break;
                        case self::TW_STATUS_CANCELED:
                            $canceled++;
                            break;
                    }
                }
            }
            $item['time'] = $timeSignature;
            $item['weeksInterval'] = (count($weekInterval) === 2) ? $EndPoint : null;
            $item['completed'] = $completed;
            $item['no-answer'] = $noAnswer;
            $item['busy'] = $busy;
            $item['canceled'] = $canceled;
            $item['cc_Duration'] = $cc_Duration;
            $item['cc_TotalPrice'] = round($cc_TotalPrice, 2);

            $callStats[] = $item;

            // array_push($callStats, $item);
            $completed = $noAnswer = $busy = $canceled = 0;
            $cc_Duration = $cc_TotalPrice = 0;
        }
        return $callStats;
    }


    /**
     * @param array $callData
     * @return string|null
     * @throws \Exception
     */
    public static function getClientTime(array $callData): ?string
    {
        $country = $callData['FromCountry'] ?? '';
        $city = $callData['FromCity'] ?? '';
        $state = $callData['FromState'] ?? '';

        if (empty($country)) {
            return null;
        }

        $region = $state ?: $city ?: '';
        $timezone = get_time_zone($country, $region);
        $timezone = $timezone ?: self::getTimezoneByCountryCode($country) ?: false;

        if (!$timezone) {
            return null;
        }

        $date = new DateTime('now', new DateTimeZone($timezone));
        return $date->format('P');
    }


    /**
     * @param string $code
     * @return string|null
     * @throws \Exception
     */
    private static function getTimezoneByCountryCode(string $code): ?string
    {
        if (empty($code)) {
            throw new \Exception('Country code is empty');
        }

        $timezone = null;
        $countriesTimeZone = DateTimeZone::listIdentifiers(DateTimeZone::PER_COUNTRY, $code);
        $countTimeZones = count($countriesTimeZone);

        if ($countTimeZones === 3) {
            $timezone = $countriesTimeZone[1] ?? null;
        } elseif ($countTimeZones < 3) {
            $timezone = $countTimeZones[0] ?? null;
        }
        return $timezone;
    }


    /**
     * @param string $countryCode
     * @return string|null
     */
    public static function getDisplayRegion(string $countryCode): ?string
    {
        if (empty($countryCode)) {
            return null;
        }

        return Locale::getDisplayRegion('-' . $countryCode, 'en');
    }

    /**
     * @return bool
     */
    public function isRingingTw(): bool
    {
        return $this->c_call_status === self::TW_STATUS_RINGING;
    }

    /**
     * @return bool
     */
    public function isInProgressTw(): bool
    {
        return $this->c_call_status === self::TW_STATUS_IN_PROGRESS;
    }

    /**
     * @return bool
     */
    public function isQueueTw(): bool
    {
        return $this->c_call_status === self::TW_STATUS_QUEUE;
    }

    /**
     * @return bool
     */
    public function isBusyTw(): bool
    {
        return $this->c_call_status === self::TW_STATUS_BUSY;
    }

    /**
     * @return bool
     */
    public function isCanceledTw(): bool
    {
        return $this->c_call_status === self::TW_STATUS_CANCELED;
    }

    /**
     * @return bool
     */
    public function isCompletedTw(): bool
    {
        return $this->c_call_status === self::TW_STATUS_COMPLETED;
    }

    /**
     * @return bool
     */
    public function isNoAnswerTw(): bool
    {
        return $this->c_call_status === self::TW_STATUS_NO_ANSWER;
    }

    /**
     * @return bool
     */
    public function isFailedTw(): bool
    {
        return $this->c_call_status === self::TW_STATUS_FAILED;
    }

    public function setTypeIn(): void
    {
        $this->c_call_type_id = self::CALL_TYPE_IN;
    }

    public function isIn(): bool
    {
        return (int) $this->c_call_type_id === self::CALL_TYPE_IN;
    }

    public function setTypeOut(): void
    {
        $this->c_call_type_id = self::CALL_TYPE_OUT;
    }

    public function isOut(): bool
    {
        return (int) $this->c_call_type_id === self::CALL_TYPE_OUT;
    }

    public function setTypeJoin(): void
    {
        $this->c_call_type_id = self::CALL_TYPE_JOIN;
    }

    public function setType(int $type): void
    {
        $this->c_call_type_id = $type;
    }

    public function isJoin(): bool
    {
        return (int) $this->c_call_type_id === self::CALL_TYPE_JOIN;
    }

    public function setTypeReturn(): void
    {
        $this->c_call_type_id = self::CALL_TYPE_RETURN;
    }

    public function isReturn(): bool
    {
        return (int) $this->c_call_type_id === self::CALL_TYPE_RETURN;
    }

    public function direct(): void
    {
        $this->c_source_type_id = self::SOURCE_DIRECT_CALL;
    }

    public function isDirect(): bool
    {
        return $this->c_source_type_id === self::SOURCE_DIRECT_CALL;
    }

    public function isRedialCall(): bool
    {
        return $this->c_source_type_id === self::SOURCE_REDIAL_CALL;
    }

    public function isRedirectCall(): bool
    {
        return $this->c_source_type_id === self::SOURCE_REDIRECT_CALL;
    }

    public function isGeneralLine(): bool
    {
        return $this->c_source_type_id === self::SOURCE_GENERAL_LINE;
    }


    /**
     * @param string $statusName
     * @return int|null
     */
    public static function getStatusByTwilioStatus(string $statusName): ?int
    {
        $statusId = null;
        switch ($statusName) {
            case self::TW_STATUS_QUEUE:
                $statusId = self::STATUS_QUEUE;
                break;
            case self::TW_STATUS_RINGING:
                $statusId = self::STATUS_RINGING;
                break;
            case self::TW_STATUS_COMPLETED:
                $statusId = self::STATUS_COMPLETED;
                break;
            case self::TW_STATUS_CANCELED:
                $statusId = self::STATUS_CANCELED;
                break;
            case self::TW_STATUS_FAILED:
                $statusId = self::STATUS_FAILED;
                break;
            case self::TW_STATUS_BUSY:
                $statusId = self::STATUS_BUSY;
                break;
            case self::TW_STATUS_IN_PROGRESS:
                $statusId = self::STATUS_IN_PROGRESS;
                break;
            case self::TW_STATUS_NO_ANSWER:
                $statusId = self::STATUS_NO_ANSWER;
                break;
        }
        return $statusId;
    }

    /**
     * @param string $statusName
     * @return int|null
     */
    public function setStatusByTwilioStatus(string $statusName): ?int
    {
        $this->c_status_id = self::getStatusByTwilioStatus($statusName);
        return $this->c_status_id;
    }

    public function isEqualTwStatus(string $status): bool
    {
        return $this->c_call_status === $status;
    }


    /**
     * @return bool
     */
    public function isStatusRinging(): bool
    {
        return $this->c_status_id === self::STATUS_RINGING;
    }

    /**
     * @return bool
     */
    public function isStatusInProgress(): bool
    {
        return $this->c_status_id === self::STATUS_IN_PROGRESS;
    }

    /**
     * @return bool
     */
    public function isStatusIvr(): bool
    {
        return $this->c_status_id === self::STATUS_IVR;
    }

    /**
     * @return int
     */
    public function setStatusIvr(): int
    {
        return $this->c_status_id = self::STATUS_IVR;
    }

    /**
     * @return bool
     */
    public function isStatusQueue(): bool
    {
        return $this->c_status_id === self::STATUS_QUEUE;
    }

    /**
     * @return int
     */
    public function setStatusQueue(): int
    {
        if (!$this->isStatusQueue()) {
            $this->c_queue_start_dt = date('Y-m-d H:i:s');
        }
        return $this->c_status_id = self::STATUS_QUEUE;
    }

    /**
     * @return int
     */
    public function setStatusCanceled(): int
    {
        return $this->c_status_id = self::STATUS_CANCELED;
    }

    /**
     * @return int
     */
    public function setStatusRinging(): int
    {
        return $this->c_status_id = self::STATUS_RINGING;
    }

    /**
     * @return bool
     */
    public function isStatusBusy(): bool
    {
        return $this->c_status_id === self::STATUS_BUSY;
    }

    /**
     * @return bool
     */
    public function isStatusCanceled(): bool
    {
        return $this->c_status_id === self::STATUS_CANCELED;
    }

    /**
     * @return bool
     */
    public function isStatusDeclined(): bool
    {
        return $this->c_status_id === self::STATUS_DECLINED;
    }

    /**
     * @return bool
     */
    public function isStatusCompleted(): bool
    {
        return (int) $this->c_status_id === self::STATUS_COMPLETED;
    }

    /**
     * @return int
     */
    public function setStatusCompleted(): int
    {
        return $this->c_status_id = self::STATUS_COMPLETED;
    }

    /**
     * @return bool
     */
    public function isStatusNoAnswer(): bool
    {
        return $this->c_status_id === self::STATUS_NO_ANSWER;
    }

    /**
     * @return bool
     */
    public function isStatusFailed(): bool
    {
        return $this->c_status_id === self::STATUS_FAILED;
    }

    /**
     * @return int
     */
    public function setStatusFailed(): int
    {
        return $this->c_status_id = self::STATUS_FAILED;
    }

    /**
     * @return bool
     */
    public function isStatusDelay(): bool
    {
        return $this->c_status_id === self::STATUS_DELAY;
    }

    /**
     * @return int
     */
    public function setStatusDelay(): int
    {
        return $this->c_status_id = self::STATUS_DELAY;
    }

    public function cancel(): void
    {
        $this->c_status_id = self::STATUS_CANCELED;
    }

    public function declined(): void
    {
        $this->c_status_id = self::STATUS_DECLINED;
    }

    /**
     * @return bool
     */
    public function isDeclined(): bool
    {
        return $this->c_status_id === self::STATUS_DECLINED;
    }

    public function hold(): void
    {
        $this->c_status_id = self::STATUS_HOLD;
    }

    public function isHold(): bool
    {
        return $this->c_status_id === self::STATUS_HOLD;
    }

    /**
     * @return bool
     */
    public function isEnded(): bool
    {
        return $this->isStatusCompleted() || $this->isStatusBusy() || $this->isStatusNoAnswer() || $this->isStatusCanceled() || $this->isStatusFailed();
    }

    /**
     * @param int $userId
     * @return bool
     */
    public function isOwner(int $userId): bool
    {
        return $this->c_created_user_id === $userId;
    }

    /**
     * @return bool
     */
    public function isTransfer(): bool
    {
        return $this->c_is_transfer ? true : false;
    }

    /**
     * @return bool
     */
    public function isSourceTransfer(): bool
    {
        return $this->c_source_type_id === self::SOURCE_TRANSFER_CALL;
    }

    /**
     * @return bool
     */
    public function cancelCall(): bool
    {
        $communication = \Yii::$app->comms;
        $callbackUrl = Yii::$app->params['url_api'] . '/twilio/cancel-call?id=' . $this->c_id;
        $data = [];
        $data['c_id'] = $this->c_id;

        if ($this->c_call_sid) {
            try {
                $result = $communication->redirectCall($this->c_call_sid, $data, $callbackUrl);
                if (!empty($result['error'])) {
                    Yii::error($result['error'], 'Call:cancelCall:redirectCall');
                }
                return true;
            } catch (\Throwable $throwable) {
                Yii::error($throwable->getMessage(), 'Call:cancelCall:Throwable');
            }
        } else {
            Yii::error(' Not found Call Sid, CallId: ' . $this->c_id, 'Call:cancelCall:Throwable');
        }

        return false;
    }

    /**
     * @return bool
     */
    public function checkCancelCall(): bool
    {
        $timeLimit = (int)(Yii::$app->params['settings']['call_incoming_time_limit'] ?? 0);

        $callMaxTime = strtotime($this->c_updated_dt) + ($timeLimit * 60);

        //Yii::info('CallId: ' . $this->c_id . ', (cd: '.$this->c_created_dt.', ud: '.$this->c_updated_dt.'), time limit: ' . $timeLimit . ' min, status: ' . $this->getStatusName() . ', Last Time (sec): ' . ($callMaxTime - time()) ,'info\checkCancelCall');

        if ($timeLimit && ($this->isStatusIvr() || $this->isStatusQueue())) {
            if ($callMaxTime < time()) {
                $result = $this->cancelCall();
//                Yii::info('CallId: ' . $this->c_id . ', '. VarDumper::dumpAsString($result) ,'info\checkCancelCall:cancelCall');
                return true;
            }
        }
        return false;
    }

    /**
     * @return string
     */
    public function getRecordingUrl(): string
    {
        return $this->c_recording_sid ? Yii::$app->comms->getCallRecordingUrl($this->c_call_sid) : '';
    }

    public function isConferenceType(): bool
    {
        return $this->c_is_conference ? true : false;
    }

    public function setConferenceType(): void
    {
        $this->c_is_conference = true;
    }

    public function getCallerName(?string $fromNumber)
    {
        if ($this->cClient) {
            return $this->cClient->getShortName();
        }

        if (!$fromNumber) {
            return 'ClientName';
        }

        $phone = PhoneList::find()->byPhone($fromNumber)->enabled()->one();

        if ($phone && $phone->departmentPhoneProject && $phone->departmentPhoneProject->dppDep && $phone->departmentPhoneProject->isEnabled()) {
            /** @var $department Department */
            $department = $phone->departmentPhoneProject->dppDep;
            return $department->dep_name;
        }

        $userProjectParams = UserProjectParams::find()->byPhone($fromNumber)->one();
        if ($userProjectParams && $userProjectParams->uppUser) {
            return $userProjectParams->uppUser->nickname;
        }

        return 'ClientName';
    }

    public function isInternal(): bool
    {
        return $this->c_source_type_id === self::SOURCE_INTERNAL;
    }

    /**
     * @return Data
     */
    public function getData(): Data
    {
        if ($this->data !== null) {
            return $this->data;
        }
        $this->data = new Data($this->c_data_json);
        return $this->data;
    }

    /**
     * @param Data $data
     */
    private function setData(Data $data): void
    {
        $this->c_data_json = $data->toJson();
        $this->data = $data;
    }

    public function recordingDisable(): void
    {
        $this->c_recording_disabled = true;
    }

    public function recordingEnable(): void
    {
        $this->c_recording_disabled = false;
    }

    public function isRecordingDisable(): bool
    {
        return $this->c_recording_disabled ? true : false;
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function getApiData(): array
    {
        $data = $this->attributes;
        $callUserAccesses = CallUserAccess::find()->select(['cua_user_id', 'cua_status_id', 'cua_created_dt'])
            ->where(['cua_call_id' => $this->c_id])
            ->orderBy(['cua_created_dt' => SORT_ASC])
            //->limit(random_int(4, 7))
            ->asArray()->all();

        $data['userAccessList'] = $callUserAccesses;
        if ($this->c_client_id) {
            $client = Client::find()->select(['first_name', 'last_name'])
                ->where(['id' => $this->c_client_id])
                ->one();
            if ($client) {
                $data['client'] = [
                    'first_name' => $client->first_name,
                    'last_name' => $client->last_name,
                    'middle_name' => $client->middle_name
                ];
            }
        }
        return $data;
    }

    /**
     * @param string $action
     * @return false|mixed
     */
    public function sendFrontendData(string $action = 'update')
    {
        $enabled = !empty(Yii::$app->params['centrifugo']['enabled']);
        if ($enabled) {
            try {
                return Yii::$app->centrifugo->setSafety(false)
                ->publish(
                    self::CHANNEL_REALTIME_MAP,
                    [
                        'object' => 'call',
                        'action' => $action,
                        'id' => $this->c_id,
                        'data' => [
                            'call' => $this->getApiData(),
                        ]
                    ]
                );
            } catch (\Throwable $throwable) {
                Yii::error(AppHelper::throwableFormatter($throwable), 'Call:sendFrontendData:Throwable');
                return false;
            }
        }
    }

    public function setDataCreatedParams(array $params): void
    {
        $data = $this->getData();
        $data->createdParams = $params;
        $this->setData($data);
    }

    public function setDataPhoneListId(?int $phoneListId): void
    {
        $data = $this->getData();
        $data->phoneListId = $phoneListId;
        $this->setData($data);
    }

    public function getDataPhoneListId(): ?int
    {
        return $this->getData()->phoneListId;
    }

    public function setDataPriority(int $value): void
    {
        $data = $this->getData();
        $data->priority = $value;
        $this->setData($data);
    }

    public function getDataPriority(): int
    {
        return $this->getData()->priority;
    }

    public function setDataRepeat($jobId, $departmentPhoneId, $createdJobTime): void
    {
        $data = $this->getData();
        $data->repeat = new Repeat([
            'jobId' => $jobId,
            'departmentPhoneId' => $departmentPhoneId,
            'createdJobTime' => $createdJobTime
        ]);
        $this->setData($data);
    }

    public function resetDataRepeat(): void
    {
        $data = $this->getData();
        $data->repeat->reset();
        $this->setData($data);
    }

    public function setDataQueueLongTime($jobId, $departmentPhoneId, $createdJobTime): void
    {
        $data = $this->getData();
        $data->queueLongTime = new QueueLongTime([
            'jobId' => $jobId,
            'departmentPhoneId' => $departmentPhoneId,
            'createdJobTime' => $createdJobTime
        ]);
        $this->setData($data);
    }

    public function resetDataQueueLongTime(): void
    {
        $data = $this->getData();
        $data->queueLongTime->reset();
        $this->setData($data);
    }

    public function setDataCreatorType(?int $id): void
    {
        $data = $this->getData();
        $data->creatorType = new CreatorType([
            'id' => $id,
        ]);
        $this->setData($data);
    }

    public function getDataCreatorType(): CreatorType
    {
        return $this->getData()->creatorType;
    }

    public function creatorTypeIsAgent(): bool
    {
        return $this->getDataCreatorType()->isAgent();
    }

    /**
     * @return array
     */
    public static function getStatusListApi(): array
    {
        $data = [];
        if (self::STATUS_LIST) {
            foreach (self::STATUS_LIST as $id => $name) {
                $data[] = [
                    'id' => $id,
                    'name' => $name,
                ];
            }
        }
        return $data;
    }

    /**
     * @return array
     */
    public static function getSourceListApi(): array
    {
        $data = [];
        if (self::SHORT_SOURCE_LIST) {
            foreach (self::SHORT_SOURCE_LIST as $id => $name) {
                $data[] = [
                    'id' => $id,
                    'name' => $name,
                ];
            }
        }
        return $data;
    }

    /**
     * @return array
     */
    public static function getTypeListApi(): array
    {
        $data = [];
        if (self::TYPE_LIST) {
            foreach (self::TYPE_LIST as $id => $name) {
                $data[] = [
                    'id' => $id,
                    'name' => $name,
                ];
            }
        }
        return $data;
    }


    public function isClientNotification(): bool
    {
        return $this->c_source_type_id === self::SOURCE_CLIENT_NOTIFICATION;
    }

    public static function getStirStatusByVerstatKey(string $verstatKey): ?string
    {
        return self::STIR_VERSTAT_LIST[$verstatKey] ?? null;
    }

    public static function isTrustedVerstat(string $key): bool
    {
        return in_array($key, self::STIR_TRUSTED_GROUP);
    }

    public function isActiveStatus(): bool
    {
        return in_array($this->c_status_id, [self::STATUS_RINGING, self::STATUS_IN_PROGRESS], true);
    }

    public function getClientPhoneNumber(): ?string
    {
        return $this->isIn() ? $this->c_from : $this->c_to;
    }

    public function getInternalPhoneNumber(): ?string
    {
        return $this->isIn() ? $this->c_to : $this->c_from;
    }
}
