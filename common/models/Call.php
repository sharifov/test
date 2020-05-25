<?php

namespace common\models;

use common\components\purifier\Purifier;
use common\models\query\CallQuery;
use frontend\widgets\notification\NotificationMessage;
use sales\access\EmployeeDepartmentAccess;
use sales\dispatchers\NativeEventDispatcher;
use sales\entities\cases\Cases;
use sales\entities\cases\CasesStatus;
use sales\entities\EventTrait;
use sales\events\call\CallCreatedEvent;
use sales\helpers\cases\CasesUrlHelper;
use sales\helpers\lead\LeadUrlHelper;
use sales\model\call\entity\call\events\CallEvents;
use sales\model\callLog\services\CallLogTransferService;
use sales\model\phoneList\entity\PhoneList;
use sales\repositories\cases\CasesRepository;
use sales\repositories\lead\LeadRepository;
use sales\services\cases\CasesManageService;
use sales\services\lead\qcall\Config;
use sales\services\lead\qcall\FindPhoneParams;
use sales\services\lead\qcall\FindWeightParams;
use sales\services\lead\qcall\QCallService;
use webapi\src\services\communication\RequestDataDTO;
use Yii;
use DateTime;
use common\components\ChartTools;
use yii\behaviors\TimestampBehavior;
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
 * @property string $c_conference_sid
 * @property string $c_is_conference
 *
 * @property string $c_recording_url
 * @property bool $c_is_new
 * @property string $recordingUrl
 *
 * @property Employee $cCreatedUser
 * @property Cases $cCase
 * @property Client $cClient
 * @property Department $cDep
 * @property Lead $cLead
 * @property Call $cParent
 * @property Call[] $calls
 * @property Project $cProject
 * @property CallUserAccess[] $callUserAccesses
 * @property Employee[] $cuaUsers
 * @property CallUserGroup[] $callUserGroups
 * @property UserGroup[] $cugUgs
 * @property Cases[] $cases
 * @property ConferenceParticipant[] $conferenceParticipants
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
    ];


    public const CALL_TYPE_OUT  = 1;
    public const CALL_TYPE_IN   = 2;

    public const CALL_TYPE_LIST = [
        self::CALL_TYPE_OUT => 'Outgoing',
        self::CALL_TYPE_IN  => 'Incoming',
    ];


    public const SOURCE_GENERAL_LINE    = 1;
    public const SOURCE_DIRECT_CALL     = 2;
    public const SOURCE_REDIRECT_CALL   = 3;
    public const SOURCE_TRANSFER_CALL   = 4;
    public const SOURCE_CONFERENCE_CALL = 5;
    public const SOURCE_REDIAL_CALL     = 6;

    public const SOURCE_LIST = [
        self::SOURCE_GENERAL_LINE => 'General Line',
        self::SOURCE_DIRECT_CALL  => 'Direct Call',
        self::SOURCE_REDIRECT_CALL  => 'Redirect Call',
        self::SOURCE_TRANSFER_CALL  => 'Transfer Call',
        self::SOURCE_CONFERENCE_CALL  => 'Conference Call',
        self::SOURCE_REDIAL_CALL  => 'Redial Call',
    ];

    public const SHORT_SOURCE_LIST = [
        self::SOURCE_GENERAL_LINE => 'General',
        self::SOURCE_DIRECT_CALL  => 'Direct',
        self::SOURCE_REDIRECT_CALL  => 'Redirect',
        self::SOURCE_TRANSFER_CALL  => 'Transfer',
        self::SOURCE_CONFERENCE_CALL  => 'Conference',
        self::SOURCE_REDIAL_CALL  => 'Redial',
    ];

    public const TW_RECORDING_STATUS_PAUSED = 'paused';
    public const TW_RECORDING_STATUS_IN_PROGRESS = 'in-progress';
    public const TW_RECORDING_STATUS_STOPPED = 'stopped';

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

            ['c_conference_sid', 'string', 'max' => 34],

            ['c_is_conference', 'default', 'value' => false],
            ['c_is_conference', 'boolean'],
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
            'c_conference_sid' => 'Conference SID',
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
        ];
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
        $createdUserId
    ): self
    {
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
		$call->setStatusIvr();
		return $call;
	}

	public function assignParentCall(int $callId, int $projectId, int $depId, int $sourceTypeId): void
	{
		$this->c_parent_id = $callId;
		$this->c_project_id = $projectId;
		$this->c_dep_id = $depId;
		$this->c_source_type_id = $sourceTypeId;
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
     * @return \yii\db\ActiveQuery
     */
    public function getConferenceParticipants()
    {
        return $this->hasMany(ConferenceParticipant::class, ['cp_call_id' => 'c_id']);
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCallUserAccesses()
    {
        return $this->hasMany(CallUserAccess::class, ['cua_call_id' => 'c_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCuaUsers()
    {
        return $this->hasMany(Employee::class, ['id' => 'cua_user_id'])->viaTable('call_user_access', ['cua_call_id' => 'c_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCallUserGroups()
    {
        return $this->hasMany(CallUserGroup::class, ['cug_c_id' => 'c_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCugUgs()
    {
        return $this->hasMany(UserGroup::class, ['ug_id' => 'cug_ug_id'])->viaTable('call_user_group', ['cug_c_id' => 'c_id']);
    }


    public function getCases()
    {
        return $this->hasMany(Cases::class, ['cs_call_id' => 'c_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCCase()
    {
        return $this->hasOne(Cases::class, ['cs_id' => 'c_case_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCClient()
    {
        return $this->hasOne(Client::class, ['id' => 'c_client_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCDep()
    {
        return $this->hasOne(Department::class, ['dep_id' => 'c_dep_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCCreatedUser()
    {
        return $this->hasOne(Employee::class, ['id' => 'c_created_user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCProject()
    {
        return $this->hasOne(Project::class, ['id' => 'c_project_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCLead()
    {
        return $this->hasOne(Lead::class, ['id' => 'c_lead_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCParent()
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
     * @return \yii\db\ActiveQuery
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
        return self::CALL_TYPE_LIST[$this->c_call_type_id] ?? '-';
    }

    public static function getCallTypeNameById(int $type)
	{
		return self::CALL_TYPE_LIST[$type] ?? '-';
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
        $isChangedStatus = isset($changedAttributes['c_status_id']);

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

        if ($this->c_parent_id && ($insert || $isChangedStatus) && $this->c_lead_id && $this->isOut() && $this->isEnded()) {

            $lead = $this->cLead;

            if (($lqc = LeadQcall::findOne($this->c_lead_id)) && time() > strtotime($lqc->lqc_dt_from)) {

                $lf = LeadFlow::find()->where(['lead_id' => $this->c_lead_id])->orderBy(['id' => SORT_DESC])->limit(1)->one();
                if ($lf) {
                    $lf->lf_out_calls = (int)$lf->lf_out_calls + 1;
                    if (!$lf->update()) {
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
                }
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
                    'cua_status_id' => CallUserAccess::STATUS_TYPE_PENDING,
                    'cua_call_id' => $this->c_id
                ])->all();
                if ($callUserAccessAny) {
                    foreach ($callUserAccessAny as $callAccess) {
                        $callAccess->noAnsweredCall();
                        if (!$callAccess->update()) {
                            Yii::error(VarDumper::dumpAsString($callAccess->errors),
                                'Call:afterSave:CallUserAccess:update');
                        }
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
                            $cuaExists = CallUserAccess::find()->andWhere([
                                'cua_call_id' => $this->c_id, 'cua_status_id' => CallUserAccess::STATUS_TYPE_ACCEPT
                            ])->andWhere(['>=', 'cua_updated_dt', $this->c_queue_start_dt])->exists();
                            if (!$cuaExists) {
                                $this->c_status_id = self::STATUS_NO_ANSWER;
                                self::updateAll(['c_status_id' => self::STATUS_NO_ANSWER], ['c_id' => $this->c_id]);
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


            if (($this->c_lead_id || $this->c_case_id) && $isChangedStatus && $this->isIn() && $this->isStatusInProgress() && in_array($changedAttributes['c_status_id'], [self::STATUS_RINGING, self::STATUS_QUEUE], true)) {

                $host = \Yii::$app->params['url_address'] ?? '';

                if ($this->c_lead_id && (int)$this->c_dep_id === Department::DEPARTMENT_SALES) {

                    $lead = $this->cLead;

                    if ($lead && !$lead->employee_id && $this->c_created_user_id && $lead->isPending()) {
                        //Yii::info(VarDumper::dumpAsString(['changedAttributes' => $changedAttributes, 'Call' => $this->attributes, 'Lead' => $lead->attributes]), 'info\Call:Lead:afterSave');
                        try {

                            $lead->answered();
                            $lead->processing($this->c_created_user_id, null, LeadFlow::DESCRIPTION_CALL_AUTO_CREATED_LEAD);
                            $leadRepository->save($lead);

                            $qCallService->remove($lead->id);

                            if ($ntf = Notifications::create($lead->employee_id, 'AutoCreated new Lead (' . $lead->id . ')', 'A new Lead (Id: ' . Purifier::createLeadShortLink($lead) . ') has been created for you. Call Id: ' . $this->c_id, Notifications::TYPE_SUCCESS, true)) {
                                $dataNotification = (Yii::$app->params['settings']['notification_web_socket']) ? NotificationMessage::add($ntf) : [];
                                Notifications::publish('getNewNotification', ['user_id' => $lead->employee_id], $dataNotification);
                            }
//                            $userListSocketNotification[$lead->employee_id] = $lead->employee_id;
                            // Notifications::publish('openUrl', ['user_id' => $lead->employee_id], ['url' => $host . '/lead/view/' . $lead->gid], false);

                            $pubChannel = UserConnection::getLastUserChannel($lead->employee_id);
                            Notifications::pub([$pubChannel], 'openUrl', ['url' => $host . '/lead/view/' . $lead->gid]);

                        } catch (\Throwable $e) {
                            Yii::error('CallId: ' . $this->c_id . ' LeadId: ' . $lead->id . ' Message: ' . $e->getMessage(), 'Call:afterSave:Lead:Answered:Processing');
                        }
                    }
//                    $lead = $this->cLead;
//
//                    if ($lead && !$lead->employee_id && $this->c_created_user_id && $lead->status === Lead::STATUS_PENDING) {
//                        Yii::info(VarDumper::dumpAsString(['changedAttributes' => $changedAttributes, 'Call' => $this->attributes, 'Lead' => $lead->attributes]), 'info\Call:Lead:afterSave');
//                        $lead->employee_id = $this->c_created_user_id;
//                        $lead->status = Lead::STATUS_PROCESSING;
//                        // $lead->l_call_status_id = Lead::CALL_STATUS_PROCESS;
//                        $lead->l_answered = true;
//                        if ($lead->save()) {
//                            Notifications::create($lead->employee_id, 'AutoCreated new Lead (' . $lead->id . ')', 'A new lead (' . $lead->id . ') has been created for you. Call Id: ' . $this->c_id, Notifications::TYPE_SUCCESS, true);
//                            $userListSocketNotification[$lead->employee_id] = $lead->employee_id;
//                            Notifications::sendSocket('openUrl', ['user_id' => $lead->employee_id], ['url' => $host . '/lead/view/' . $lead->gid], false);
//                        } else {
//                            Yii::error(VarDumper::dumpAsString($lead->errors), 'Call:afterSave:Lead:update');
//                        }
//                    }
                }


                if ($this->c_case_id && ((int)$this->c_dep_id === Department::DEPARTMENT_EXCHANGE || (int)$this->c_dep_id === Department::DEPARTMENT_SUPPORT)) {
                    $case = $this->cCase;

                    if ($case && !$case->cs_user_id && $this->c_created_user_id && $case->isPending()) {
                        // Yii::info(VarDumper::dumpAsString(['changedAttributes' => $changedAttributes, 'Call' => $this->attributes, 'Case' => $case->attributes]), 'info\Call:Case:afterSave');
//                        $case->cs_user_id = $this->c_created_user_id;
//                        $case->cs_status = CasesStatus::STATUS_PROCESSING;

//                        if ($case->save()) {
//                            Notifications::create($case->cs_user_id, 'AutoCreated new Case (' . $case->cs_id . ')', 'A new Case (' . $case->cs_id . ') has been created for you. Call Id: ' . $this->c_id, Notifications::TYPE_SUCCESS, true);
//                            $userListSocketNotification[$case->cs_user_id] = $case->cs_user_id;
//                            Notifications::sendSocket('openUrl', ['user_id' => $case->cs_user_id], ['url' => $host . '/cases/view/' . $case->cs_gid], false);
//                        } else {
//                            Yii::error(VarDumper::dumpAsString($case->errors), 'Call:afterSave:Case:update');
//                        }

                        try {

                            $casesManageService = Yii::createObject(CasesManageService::class);
                            $casesManageService->callAutoTake($this->c_case_id, $this->c_created_user_id, null, 'Call auto take');

//                            $caseRepo = Yii::createObject(CasesRepository::class);
//                            $case->processing((int)$this->c_created_user_id, null);
//                            $caseRepo->save($case);

                            if ($ntf = Notifications::create($this->c_created_user_id, 'AutoCreated new Case (' . $case->cs_id . ')', 'A new Case (Id: ' . Purifier::createCaseShortLink($case) . ') has been created for you. Call Id: ' . $this->c_id, Notifications::TYPE_SUCCESS, true)) {
                                $dataNotification = (Yii::$app->params['settings']['notification_web_socket']) ? NotificationMessage::add($ntf) : [];
                                Notifications::publish('getNewNotification', ['user_id' => $this->c_created_user_id], $dataNotification);
                            }

//                            $userListSocketNotification[$case->cs_user_id] = $case->cs_user_id;
                            // Notifications::publish('openUrl', ['user_id' => $case->cs_user_id], ['url' => $host . '/cases/view/' . $case->cs_gid], false);
                            $pubChannel = UserConnection::getLastUserChannel($this->c_created_user_id);
                            Notifications::pub([$pubChannel], 'openUrl', ['url' => $host . '/cases/view/' . $case->cs_gid]);
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
            }

            if ($this->c_lead_id && $this->cLead && $this->cLead->employee_id) {
                $userListNotifications[$this->cLead->employee_id] = $this->cLead->employee_id;
            }

            if ($this->c_case_id && $this->cCase && $this->cCase->cs_user_id) {
                $userListNotifications[$this->cCase->cs_user_id] = $this->cCase->cs_user_id;
            }

            if ($userListNotifications) {
                $title = 'Missed Call (' . $this->getSourceName() . ')';
                $message = 'Missed Call (' . $this->getSourceName() . ')  from ' . $this->c_from . ' to ' . $this->c_to;
                if ($this->c_lead_id && $this->cLead) {
                    $message .= ', Lead (Id: ' . Purifier::createLeadShortLink($this->cLead) . ')';
                }

                if ($this->c_case_id && $this->cCase) {
                    $message .= ', Case (Id: ' . Purifier::createCaseShortLink($this->cCase) . ')';
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

        if($this->c_lead_id && ($lead = $this->cLead)) {
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
                $this->cLead->updateLastAction();
            }
        }

        if ($this->cCase) {
            $this->cCase->updateLastAction();
        }



        if ($isChangedStatus) {
            NativeEventDispatcher::recordEvent(CallEvents::class, CallEvents::CHANGE_STATUS, [CallEvents::class, 'updateUserStatus'], ['call' => $this, 'changedAttributes' => $changedAttributes]);
            NativeEventDispatcher::trigger(CallEvents::class, CallEvents::CHANGE_STATUS);
        }


        if ($this->c_created_user_id && ($insert || $isChangedStatus))  {
            //Notifications::socket($this->c_created_user_id, $this->c_lead_id, 'callUpdate', ['id' => $this->c_id, 'status' => $this->getStatusName(), 'duration' => $this->c_call_duration, 'snr' => $this->c_sequence_number], true);

            Notifications::publish('callUpdate', ['user_id' => $this->c_created_user_id, 'lead_id' => $this->c_lead_id, 'case_id' => $this->c_case_id],
                [
                	'id' => $this->c_id,
					'status' => $this->getStatusName(),
					'duration' => $this->c_call_duration,
					'snr' => $this->c_sequence_number,
					'leadId' => $this->c_lead_id,
					'isIn' => $this->isIn(),
					'phoneFrom' => $this->c_from,
					'name' => $this->cClient ? $this->cClient->getFullName() : '',
					'fromInternal' => PhoneList::find()->byPhone($this->c_from)->enabled()->exists()
				]
			);
        }

        if ($this->c_lead_id || $this->c_case_id) {
            //Notifications::socket(null, $this->c_lead_id, 'updateCommunication', ['lead_id' => $this->c_lead_id, 'status_id' => $this->c_status_id, 'status' => $this->getStatusName()], true);

            $socketParams = [];
            if ($this->c_lead_id) {
                $socketParams['lead_id'] = $this->c_lead_id;
            }

            if ($this->c_case_id) {
                $socketParams['case_id'] = $this->c_case_id;
            }

            Notifications::publish('updateCommunication', $socketParams, ['lead_id' => $this->c_lead_id, 'case_id' => $this->c_case_id, 'status_id' => $this->c_status_id, 'status' => $this->getStatusName()]);
        }

//        if ($userListSocketNotification) {
//            foreach ($userListSocketNotification as $userId) {
//                Notifications::sendSocket('getNewNotification', ['user_id' => $userId]);
//            }
//            unset($userListSocketNotification);
//        }

        Notifications::pingUserMap();

        $logEnable = Yii::$app->params['settings']['call_log_enable'] ?? false;
        if ($logEnable) {
            $isChangedTwStatus = array_key_exists('c_call_status', $changedAttributes);
            $isChangedDuration = array_key_exists('c_call_duration', $changedAttributes);
            if (
                Yii::$app->id === 'app-webapi'
                && $this->isTwFinishStatus()
                && ($insert || $isChangedTwStatus || $isChangedDuration)
            ) {
                (Yii::createObject(CallLogTransferService::class))->transfer($this);
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

    }

    public function isTwFinishStatus(): bool
    {
        return $this->isCompletedTw() || $this->isBusyTw() || $this->isNoAnswerTw() || $this->isFailedTw() || $this->isCanceledTw();
    }

    /**
     * @param Call $call
     * @param int $user_id
     * @param bool $isConference
     * @return bool
     */
    public static function applyCallToAgent(Call $call, int $user_id, bool $isConference): bool
    {
        try {
            if ($call) {

                // \Yii::info('INFO: Call ('.$call->getStatusName().', '.$call->c_call_status.') CallId: ' . $call->c_id. ',  User: ' . $user_id, 'info\Call:applyCallToAgent:callRedirect');

                /*if ($call->c_created_user_id) {
                    return false;
                }*/

//				$callFromInternalPhone = PhoneList::find()->byPhone($call->c_from)->exists();

                if ($call->isStatusQueue()) {

                } else {
                    \Yii::warning('Error: Call ('.$call->getStatusName().', '.$call->c_call_status.') not in status QUEUE: ' . $call->c_id. ',  User: ' . $user_id, 'Call:applyCallToAgent:callRedirect');
                    return false;
                }

                //$call->c_call_status = self::CALL_STATUS_IN_PROGRESS;
//                if ($parentCall = $call->cParent) {
//                    //$parentCall->setStatusDelay();
//                    //$parentCall->update();
//                } else {
                    $call->setStatusDelay();
                //}

                if($call->c_created_user_id && (int) $call->c_created_user_id !== $user_id) {
                    $call->c_source_type_id = self::SOURCE_REDIRECT_CALL;

                    $user = Employee::findOne($user_id);

                    $message = 'Missed Call (' . $call->getSourceName() . ')  from ' . $call->c_from . ' to ' . $call->c_to . '. Taken by Agent: ' . ($user ? Html::encode($user->username) : '-');
                    if ($call->c_lead_id && $call->cLead) {
                        $message .= ', Lead (Id: ' . Purifier::createLeadShortLink($call->cLead) . ')';
                    }
                    if ($call->c_case_id && $call->cCase) {
                        $message .= ', Case (Id: ' . Purifier::createCaseShortLink($call->cCase) . ')';
                    }

                    if ($ntf = Notifications::create(
                        $call->c_created_user_id,
                        'Missed Call (' . $call->getSourceName() . ')',
                        $message,
                        Notifications::TYPE_WARNING,
                        true)
                    ) {
                        // Notifications::socket($call->c_created_user_id, null, 'getNewNotification', [], true);
                        $dataNotification = (Yii::$app->params['settings']['notification_web_socket']) ? NotificationMessage::add($ntf) : [];
                        Notifications::publish('getNewNotification', ['user_id' => $call->c_created_user_id], $dataNotification);
                    }




                    //Notifications::create($call->c_source_type_id, 'New incoming Call (' . $this->cua_call_id . ')', 'New incoming Call (' . $this->cua_call_id . ')', Notifications::TYPE_SUCCESS, true);
                    //Notifications::socket($this->cua_user_id, null, 'getNewNotification', [], true);

                }

                $call->c_created_user_id = $user_id;

                $callUserAccessAny = CallUserAccess::find()->where(['cua_status_id' => CallUserAccess::STATUS_TYPE_PENDING, 'cua_call_id' => $call->c_id])->andWhere(['!=', 'cua_user_id', $call->c_created_user_id])->all();
                if ($callUserAccessAny) {
                    foreach ($callUserAccessAny as $callAccess) {
                        $callAccess->noAnsweredCall();
                        if (!$callAccess->update()) {
                            Yii::error(VarDumper::dumpAsString($callAccess->errors), 'Call:applyCallToAgent:CallUserAccess:save');
                        }
                    }
                }

                if ($call->update() === false) {
                    Yii::error(VarDumper::dumpAsString(['call' => $call->getAttributes(), 'error' => $call->getErrors()]), 'Call:applyCallToAgent:call:update');
                }

                if ($isConference) {
                    if (!$call->isConference()) {
                        $call->setConferenceType();
                        $call->update();
                    }
                    $to = 'client:seller' . $user_id;
                    $res = \Yii::$app->communication->acceptConferenceCall(
                        $call->c_call_sid,
                        $to,
                        $call->c_from,
                        $call->c_to
                    );
                } else {
                    $agent = 'seller' . $user_id;
                    $res = \Yii::$app->communication->callRedirect($call->c_call_sid, 'client', $call->c_from, $agent);
                }


                if ($res && isset($res['error']) && $res['error'] === false) {
                    if (isset($res['data']['is_error']) && $res['data']['is_error'] === true) {
                        $call->c_call_status = self::TW_STATUS_CANCELED;
                        $call->setStatusByTwilioStatus($call->c_call_status);
                        $call->c_created_user_id = null;
                        $call->update();
                        return false;
                    }

                    /*$call->c_call_status = self::CALL_STATUS_RINGING;
                    $call->c_created_user_id = $user_id;
                    $call->update();*/

                    // \Yii::info(VarDumper::dumpAsString($res), 'info\Call:applyCallToAgent:callRedirect');
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
     * @return bool
     */
    public static function applyCallToAgentAccess(Call $call, int $user_id): bool
    {
        try {
            if ($call) {
                $callUserAccess = CallUserAccess::find()->where(['cua_user_id' => $user_id, 'cua_call_id' => $call->c_id])->one();
                if(!$callUserAccess) {
                    $callUserAccess = new CallUserAccess();
                    $callUserAccess->cua_call_id = $call->c_id;
                    $callUserAccess->cua_user_id = $user_id;
                    $callUserAccess->acceptPending();

                } else {

                    $callUserAccess->acceptPending();
                }

                if(!$callUserAccess->save()) {
                    Yii::error(VarDumper::dumpAsString($callUserAccess->errors), 'CallQueueJob:execute:CallUserAccess:save');
                } else {
                    return true;
                }
            }
        } catch (\Throwable $e) {
            \Yii::error($e, 'Call:applyCallToAgentAccess');
//            \Yii::error(VarDumper::dumpAsString([$e->getMessage(), $e->getFile(), $e->getLine()]), 'Call:applyCallToAgentAccess');
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
    public static function getCallStats(string $startDate, string $endDate, ?string $groupingBy, int $callType) : array
    {
        $sDate = $startDate." 00:00:00";
        $eDate = $endDate." 23:59:59";
        switch ($groupingBy){
            case null:
                if (strtotime($startDate) == strtotime($endDate)){
                    $hoursRange = ChartTools::getHoursRange($startDate, $endDate." 23:59:59", $step = '+1 hour', $format = 'H:i:s');
                } else {
                    $daysRange = ChartTools::getDaysRange($startDate, $endDate);
                }
                break;
            case 'hours':
                if (strtotime($startDate) == strtotime($endDate)){
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

        if ($callType == 0){
            $calls = self::find()->select(['c_call_status', 'c_updated_dt', 'c_call_duration', 'c_price'])
                ->where(['c_call_status' => ['completed', 'busy', 'no-answer', 'canceled']])
                ->andWhere(['between', 'c_updated_dt', $sDate, $eDate])->all();
        } else {
            $calls =self::find()->select(['c_call_status', 'c_updated_dt', 'c_call_duration', 'c_price'])
                ->where(['c_call_status' => ['completed', 'busy', 'no-answer', 'canceled']])
                ->andWhere(['between', 'c_updated_dt', $sDate, $eDate])
                ->andWhere(['=', 'c_call_type_id', $callType])->all();
        }

        $callStats = [];
        $item = [];
        if (strtotime($startDate) < strtotime($endDate)){
            if (isset($daysRange)) {
                $timeLine = $daysRange;
                $item['timeLine'] = 'd M';
                $timeInSeconds = 0;
                $dateFormat = 'Y-m-d';
            } elseif (isset($monthsRange)){
                $timeLine = $monthsRange;
                $timeInSeconds = 0;
                $dateFormat = 'Y-m';
                $item['timeLine'] = 'Y, M';
            } elseif (isset($weeksPeriods)){
                $timeLine = $weeksPeriods;
                $item['timeLine'] = 'd M';
                $timeInSeconds = 0;
                $dateFormat = 'Y-m-d';
            }elseif (isset($hoursRange)){
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
            } elseif (isset($hoursRange)){
                $timeLine = $hoursRange;
                $item['timeLine'] = 'H:i';
                $dateFormat = 'H:i:s';
                $timeInSeconds = 3600;
            } elseif (isset($monthsRange)) {
                $timeLine = $monthsRange;
                $timeInSeconds = 0;
                $dateFormat = 'Y-m';
                $item['timeLine'] = 'Y, M';
            } elseif (isset($weeksPeriods)){
                $timeLine = $weeksPeriods;
                $item['timeLine'] = 'd M';
                $timeInSeconds = 0;
                $dateFormat = 'Y-m-d';
            }
        }

        $completed = $noAnswer = $busy = $canceled = 0;
        $cc_Duration = $cc_TotalPrice= 0;
        foreach ($timeLine as $key => $timeSignature){
            $weekInterval = explode('/', $timeSignature);
            if (count($weekInterval) != 2){
                $EndPoint = date($dateFormat, strtotime($timeSignature) + $timeInSeconds);
                if ($EndPoint == '00:00:00'){
                    $EndPoint = '23:59:59';
                }
            } else {
                $EndPoint = date($dateFormat, strtotime($weekInterval[1]));
                $timeSignature = date($dateFormat, strtotime($weekInterval[0]));
            }
            foreach ($calls as $callItem){
                $callUpdatedTime = date($dateFormat, strtotime($callItem->c_updated_dt));
                if ($callUpdatedTime >= $timeSignature && $callUpdatedTime <= $EndPoint)
                {
                    switch ($callItem->c_call_status){
                        case self::TW_STATUS_COMPLETED :
                            $completed++;
                            $cc_Duration += $callItem->c_call_duration;
                            $cc_TotalPrice += $callItem->c_price;
                            break;
                        case self::TW_STATUS_NO_ANSWER :
                            $noAnswer++;
                            break;
                        case self::TW_STATUS_BUSY :
                            $busy++;
                            break;
                        case self::TW_STATUS_CANCELED :
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
            $cc_Duration = $cc_TotalPrice= 0;
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
        $timezone = geoip_time_zone_by_country_and_region($country, $region);
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

    /**
     * @return bool
     */
    public function isIn(): bool
    {
        return (int) $this->c_call_type_id === self::CALL_TYPE_IN;
    }

    /**
     * @return bool
     */
    public function isOut(): bool
    {
        return (int) $this->c_call_type_id === self::CALL_TYPE_OUT;
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
        $communication = \Yii::$app->communication;
        $callbackUrl = Yii::$app->params['url_api_address'] . '/twilio/cancel-call?id=' . $this->c_id;
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
            Yii::error(' Not found Call Sid, CallId: ' . $this->c_id , 'Call:cancelCall:Throwable');
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
                Yii::info('CallId: ' . $this->c_id . ', '. VarDumper::dumpAsString($result) ,'info\checkCancelCall:cancelCall');
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
        return $this->c_recording_sid ? Yii::$app->communication->recording_url . $this->c_recording_sid : '';
    }

    public function isConference(): bool
    {
        return $this->c_is_conference ? true : false;
    }

    public function setConferenceType(): void
    {
        $this->c_is_conference = true;
    }
}
