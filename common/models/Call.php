<?php

namespace common\models;

use sales\entities\AggregateRoot;
use sales\entities\cases\Cases;
use sales\entities\cases\CasesStatus;
use sales\entities\EventTrait;
use sales\services\cases\CasesManageService;
use Yii;
use DateTime;
use common\components\ChartTools;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
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
 * @property string $c_recording_url
 * @property int $c_recording_duration
 * @property int $c_sequence_number
 * @property int $c_lead_id
 * @property int $c_created_user_id
 * @property string $c_created_dt
 * @property int $c_com_call_id
 * @property string $c_updated_dt
 * @property int $c_project_id
 * @property string $c_error_message
 * @property bool $c_is_new
 * @property bool $c_is_deleted
 * @property float $c_price
 * @property int $c_source_type_id
 * @property int $c_dep_id
 * @property int $c_case_id
 * @property int $c_client_id
 * @property int $c_status_id
 * @property int $c_parent_id
 * @property string $c_recording_sid
 *
 * @property Employee $cCreatedUser
 * @property Cases $cCase
 * @property Client $cClient
 * @property Department $cDep
 * @property Lead $cLead
 * @property Lead2 $cLead2
 * @property Call $cParent
 * @property Call[] $calls
 * @property Project $cProject
 * @property Cases[] $cases
 * @property CallUserAccess[] $callUserAccesses
 * @property Employee[] $cuaUsers
 * @property CallUserGroup[] $callUserGroups
 * @property UserGroup[] $cugUgs
 */
class Call extends \yii\db\ActiveRecord implements AggregateRoot
{
    
    use EventTrait;

    public const CALL_STATUS_IVR            = 'ivr';
    public const CALL_STATUS_QUEUE          = 'queued';
    public const CALL_STATUS_RINGING        = 'ringing';
    public const CALL_STATUS_IN_PROGRESS    = 'in-progress';
    public const CALL_STATUS_COMPLETED      = 'completed';
    public const CALL_STATUS_BUSY           = 'busy';
    public const CALL_STATUS_NO_ANSWER      = 'no-answer';
    public const CALL_STATUS_FAILED         = 'failed';
    public const CALL_STATUS_CANCELED       = 'canceled';

    public const CALL_STATUS_LIST = [
        self::CALL_STATUS_IVR         => 'IVR',
        self::CALL_STATUS_QUEUE         => 'Queued',
        self::CALL_STATUS_RINGING       => 'Ringing',
        self::CALL_STATUS_IN_PROGRESS   => 'In progress',
        self::CALL_STATUS_COMPLETED     => 'Completed',
        self::CALL_STATUS_BUSY          => 'Busy',
        self::CALL_STATUS_NO_ANSWER     => 'No answer',
        self::CALL_STATUS_FAILED        => 'Failed',
        self::CALL_STATUS_CANCELED      => 'Canceled',
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
    ];

    public const CALL_STATUS_LABEL_LIST = [
        self::CALL_STATUS_IVR         => '<span class="label label-warning"><i class="fa fa-refresh fa-spin"></i> ' . self::CALL_STATUS_LIST[self::CALL_STATUS_IVR] . '</span>',
        self::CALL_STATUS_QUEUE         => '<span class="label label-warning"><i class="fa fa-refresh fa-spin"></i> ' . self::CALL_STATUS_LIST[self::CALL_STATUS_QUEUE] . '</span>',
        self::CALL_STATUS_RINGING       => '<span class="label label-warning"><i class="fa fa-spinner fa-spin"></i> ' . self::CALL_STATUS_LIST[self::CALL_STATUS_RINGING] . '</span>',
        self::CALL_STATUS_IN_PROGRESS   => '<span class="label label-success"><i class="fa fa-volume-control-phone"></i> ' . self::CALL_STATUS_LIST[self::CALL_STATUS_IN_PROGRESS] . '</span>',
        self::CALL_STATUS_COMPLETED     => '<span class="label label-info"><i class="fa fa-check"></i> ' . self::CALL_STATUS_LIST[self::CALL_STATUS_COMPLETED] . '</span>',
        self::CALL_STATUS_BUSY          => '<span class="label label-danger"><i class="fa fa-ban"></i> ' . self::CALL_STATUS_LIST[self::CALL_STATUS_BUSY] . '</span>',
        self::CALL_STATUS_NO_ANSWER     => '<span class="label label-danger"><i class="fa fa-times-circle"></i> ' . self::CALL_STATUS_LIST[self::CALL_STATUS_NO_ANSWER] . '</span>',
        self::CALL_STATUS_FAILED        => '<span class="label label-danger"><i class="fa fa-window-close"></i> ' . self::CALL_STATUS_LIST[self::CALL_STATUS_FAILED] . '</span>',
        self::CALL_STATUS_CANCELED      => '<span class="label label-danger"><i class="fa fa-close"></i> ' . self::CALL_STATUS_LIST[self::CALL_STATUS_CANCELED] . '</span>',
    ];


    public const STATUS_LABEL_LIST = [
        self::STATUS_IVR         => '<span class="label label-warning"><i class="fa fa-refresh fa-spin"></i> ' . self::STATUS_LIST[self::STATUS_IVR] . '</span>',
        self::STATUS_QUEUE         => '<span class="label label-warning"><i class="fa fa-refresh fa-spin"></i> ' . self::STATUS_LIST[self::STATUS_QUEUE] . '</span>',
        self::STATUS_RINGING       => '<span class="label label-warning"><i class="fa fa-spinner fa-spin"></i> ' . self::STATUS_LIST[self::STATUS_RINGING] . '</span>',
        self::STATUS_IN_PROGRESS   => '<span class="label label-success"><i class="fa fa-volume-control-phone"></i> ' . self::STATUS_LIST[self::STATUS_IN_PROGRESS] . '</span>',
        self::STATUS_COMPLETED     => '<span class="label label-info"><i class="fa fa-check"></i> ' . self::STATUS_LIST[self::STATUS_COMPLETED] . '</span>',
        self::STATUS_BUSY          => '<span class="label label-danger"><i class="fa fa-ban"></i> ' . self::STATUS_LIST[self::STATUS_BUSY] . '</span>',
        self::STATUS_NO_ANSWER     => '<span class="label label-danger"><i class="fa fa-times-circle"></i> ' . self::STATUS_LIST[self::STATUS_NO_ANSWER] . '</span>',
        self::STATUS_FAILED        => '<span class="label label-danger"><i class="fa fa-window-close"></i> ' . self::STATUS_LIST[self::STATUS_FAILED] . '</span>',
        self::STATUS_CANCELED      => '<span class="label label-danger"><i class="fa fa-close"></i> ' . self::STATUS_LIST[self::STATUS_CANCELED] . '</span>',
    ];

    public const CALL_STATUS_DESCRIPTION_LIST = [
        self::CALL_STATUS_IVR           => 'The call is IVR.',
        self::CALL_STATUS_QUEUE         => 'The call is ready and waiting in line before going out.',
        self::CALL_STATUS_RINGING       => 'The call is currently ringing.',
        self::CALL_STATUS_IN_PROGRESS   => 'The call was answered and is actively in progress.',
        self::CALL_STATUS_COMPLETED     => 'The call was answered and has ended normally.',
        self::CALL_STATUS_BUSY          => 'The caller received a busy signal.',
        self::CALL_STATUS_FAILED        => 'The call could not be completed as dialed, most likely because the phone number was non-existent.',
        self::CALL_STATUS_NO_ANSWER     => 'The call ended without being answered.',
        self::CALL_STATUS_CANCELED      => 'The call was canceled via the REST API while queued or ringing.',
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

    public const SOURCE_LIST = [
        self::SOURCE_GENERAL_LINE => 'General Line',
        self::SOURCE_DIRECT_CALL  => 'Direct Call',
        self::SOURCE_REDIRECT_CALL  => 'Redirect Call',
        self::SOURCE_TRANSFER_CALL  => 'Transfer Call',
    ];

    public const SHORT_SOURCE_LIST = [
        self::SOURCE_GENERAL_LINE => 'GL',
        self::SOURCE_DIRECT_CALL  => 'DC',
        self::SOURCE_REDIRECT_CALL  => 'RC',
        self::SOURCE_TRANSFER_CALL  => 'TC',
    ];


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'call';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['c_call_sid'], 'required'],
            [['c_call_type_id', 'c_lead_id', 'c_created_user_id', 'c_com_call_id', 'c_project_id', 'c_call_duration', 'c_recording_duration', 'c_dep_id', 'c_case_id', 'c_client_id', 'c_status_id', 'c_parent_id', 'c_sequence_number'], 'integer'],
            [['c_price'], 'number'],
            [['c_is_new'], 'default', 'value' => true],
            [['c_is_new', 'c_is_deleted'], 'boolean'],
            [['c_created_dt', 'c_updated_dt'], 'safe'],
            [['c_call_sid', 'c_parent_call_sid', 'c_recording_sid'], 'string', 'max' => 34],
            [['c_from', 'c_to', 'c_forwarded_from'], 'string', 'max' => 100],
            [['c_call_status'], 'string', 'max' => 15],
            [['c_caller_name'], 'string', 'max' => 50],
            [['c_recording_url'], 'string', 'max' => 200],
            [['c_error_message'], 'string', 'max' => 500],
            [['c_case_id'], 'exist', 'skipOnError' => true, 'targetClass' => Cases::class, 'targetAttribute' => ['c_case_id' => 'cs_id']],
            [['c_client_id'], 'exist', 'skipOnError' => true, 'targetClass' => Client::class, 'targetAttribute' => ['c_client_id' => 'id']],
            [['c_created_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['c_created_user_id' => 'id']],
            [['c_dep_id'], 'exist', 'skipOnError' => true, 'targetClass' => Department::class, 'targetAttribute' => ['c_dep_id' => 'dep_id']],
            [['c_lead_id'], 'exist', 'skipOnError' => true, 'targetClass' => Lead::class, 'targetAttribute' => ['c_lead_id' => 'id']],
            [['c_parent_id'], 'exist', 'skipOnError' => true, 'targetClass' => self::class, 'targetAttribute' => ['c_parent_id' => 'c_id']],
            [['c_project_id'], 'exist', 'skipOnError' => true, 'targetClass' => Project::class, 'targetAttribute' => ['c_project_id' => 'id']],
        ];
    }


    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'c_id' => 'ID',
            'c_call_sid' => 'Call SID',
            'c_call_type_id' => 'Call Type ID',
            'c_from' => 'From',
            'c_to' => 'To',
            'c_call_status' => 'Call Status',
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
            'c_is_deleted' => 'Is Deleted',
            'c_price' => 'Price',
            'c_source_type_id' => 'Source Type',
            'c_dep_id' => 'Department ID',
            'c_case_id' => 'Case ID',
            'c_client_id' => 'Client',
            'c_status_id' => 'Status ID',
            'c_parent_id' => 'Parent ID',
            'c_recording_sid' => 'Recording SID'
        ];
    }

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
     * @param $callSid
     * @param $callTypeId
     * @param $from
     * @param $to
     * @param $createdDt
     * @param $recordingUrl
     * @param $recordingDuration
     * @param $callerName
     * @param $projectId
     * @return Call
     */
    public static function create(
        $callSid,
        $callTypeId,
        $from,
        $to,
        $createdDt,
        $recordingUrl,
        $recordingDuration,
        $callerName,
        $projectId
    ): self
    {
        $call = new static();
        $call->c_call_sid = $callSid;
        $call->c_call_type_id = $callTypeId;
        $call->c_from = $from;
        $call->c_to = $to;
        $call->c_created_dt = $createdDt;
        $call->c_updated_dt = date('Y-m-d H:i:s');
        $call->c_recording_url = $recordingUrl;
        $call->c_recording_duration = $recordingDuration;
        $call->c_caller_name = $callerName;
        $call->c_project_id = $projectId;
        return $call;
    }

    /**
     * @param string $recordingUrl
     * @param string $recordingSid
     * @param int $recordingDuration
     */
    public function updateRecordingData(string $recordingUrl, string $recordingSid, int $recordingDuration): void
    {
        $this->c_recording_url = $recordingUrl;
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
    public function getCLead2()
    {
        return $this->hasOne(Lead2::class, ['id' => 'c_lead_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCParent()
    {
        return $this->hasOne(self::class, ['c_id' => 'c_parent_id']);
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
        return new CallQuery(get_called_class());
    }

    /**
     * @return mixed|string
     */
    public function getCallTypeName()
    {
        return self::CALL_TYPE_LIST[$this->c_call_type_id] ?? '-';
    }

    /**
     * @return mixed|string
     */
    public function getStatusName()
    {
        return self::CALL_STATUS_LIST[$this->c_call_status] ?? '-';
    }

    /**
     * @return mixed|string
     */
    public function getStatusName2()
    {
        return self::STATUS_LIST[$this->c_status_id] ?? '-';
    }

    /**
     * @return string
     */
    public function getStatusIcon(): string
    {
        if ($this->isRinging()) {
            $icon = 'fa fa-refresh fa-pulse fa-fw text-danger';
        } elseif ($this->isInProgress()) {
            $icon = 'fa fa-spinner fa-pulse fa-fw';
        } elseif ($this->isQueue()) {
            $icon = 'fa fa-pause';
        } elseif ($this->isCompleted()) {
            $icon = 'fa fa-trophy text-success';
        } elseif ($this->isCanceled() || $this->isNoAnswer() || $this->isBusy()) {
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
        return self::CALL_STATUS_LABEL_LIST[$this->c_call_status] ?? '-';
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

        if(!$insert) {
//            if(isset($changedAttributes['c_call_status']) && in_array($this->c_call_status, [self::CALL_STATUS_COMPLETED, self::CALL_STATUS_BUSY, self::CALL_STATUS_NO_ANSWER], false)) {
//                if($this->c_created_user_id) {
//                    // self::applyHoldCallToAgent($this->c_created_user_id);
//                    $job = new AgentCallQueueJob();
//                    $job->user_id = $this->c_created_user_id;
//                    $jobId = Yii::$app->queue_job->push($job);
//                }
//            }


            if(isset($changedAttributes['c_call_status']) && $this->isIn() && ($this->isCompleted() || $this->isNoAnswer() || $this->isBusy() || $this->isInProgress() || $this->isCanceled())) {

                $callUserAccessAny = CallUserAccess::find()->where(['cua_status_id' => [CallUserAccess::STATUS_TYPE_PENDING], 'cua_call_id' => $this->c_id])->all();
                if ($callUserAccessAny) {
                    foreach ($callUserAccessAny as $callAccess) {
                        $callAccess->noAnsweredCall();
                        if (!$callAccess->update()) {
                            Yii::error(VarDumper::dumpAsString($callAccess->errors), 'IncomingCallWidget:acceptCall:UserCallStatus:save');
                        }
                    }
                }

                if (!$this->c_parent_id) {
                    $isCallUserAccepted = CallUserAccess::find()->where([
                        'cua_status_id' => [CallUserAccess::STATUS_TYPE_ACCEPT],
                        'cua_call_id' => $this->c_id
                    ])->exists();
                    if (!$isCallUserAccepted) {
                        $this->c_status_id = self::STATUS_NO_ANSWER;
                        self::updateAll(['c_status_id' => self::STATUS_NO_ANSWER], ['c_id' => $this->c_id]);
                    }
                }

                if ($this->isCompleted() || $this->isCanceled() || $this->isNoAnswer() || $this->isBusy()) {

                    $callAcceptExist = CallUserAccess::find()->where(['cua_status_id' => CallUserAccess::STATUS_TYPE_ACCEPT, 'cua_call_id' => $this->c_id])->exists();
                    if (!$callAcceptExist) {
                        if ($this->c_created_user_id) {
                            Notifications::create(
                                $this->c_created_user_id,
                                'Missed Call (' . $this->getSourceName() . ')',
                                'Missed Call (' . $this->getSourceName() . ')  from ' . $this->c_from . ' to ' . $this->c_to,
                                Notifications::TYPE_WARNING,
                                true);

                            Notifications::socket($this->c_created_user_id, null, 'getNewNotification', [], true);
                        }

                        if ($this->c_lead_id && $this->cLead2 && $this->cLead2->employee_id) {
                            Notifications::create(
                                $this->cLead2->employee_id,
                                'Missed Call (' . $this->getSourceName() . ')',
                                'Missed Call (' . $this->getSourceName() . ')  from ' . $this->c_from . ' to ' . $this->c_to . ' <br>Lead ID: ' . $this->c_lead_id,
                                Notifications::TYPE_WARNING,
                                true);
                            Notifications::socket($this->cLead2->employee_id, null, 'getNewNotification', [], true);
                        }

                        if ($this->c_case_id && $this->cCase && $this->cCase->cs_user_id) {
                            Notifications::create(
                                $this->cCase->cs_user_id,
                                'Missed Call (' . $this->getSourceName() . ')',
                                'Missed Call (' . $this->getSourceName() . ') from ' . $this->c_from . ' to ' . $this->c_to . ' <br>Case ID: ' . $this->c_case_id,
                                Notifications::TYPE_WARNING,
                                true);
                            Notifications::socket($this->cCase->cs_user_id, null, 'getNewNotification', [], true);
                        }


                    }
                }
            }



            if(isset($changedAttributes['c_call_status']) && $this->isIn() && $this->c_case_id && $this->isInProgress()) {
                if($this->c_created_user_id && $this->cCase && $this->c_created_user_id !== $this->cCase->cs_user_id) {
                    try {
                        $casesManageService = Yii::createObject(CasesManageService::class);
                        $casesManageService->take($this->c_case_id, $this->c_created_user_id);
                    } catch (\Throwable $exception) {
                        Yii::error(VarDumper::dumpAsString($exception), 'Call:afterSave:CasesManageService:Case:Take');
                    }

                }
            }

            //Yii::info(VarDumper::dumpAsString($this->attributes), 'info\Call:afterSave');

            if(isset($changedAttributes['c_call_status']) && $this->isIn() && $this->c_lead_id && ($this->isNoAnswer() || $this->isCompleted())) {

//                if($this->c_call_status == self::CALL_STATUS_NO_ANSWER) {
//                    if ($this->c_created_user_id) {
//                        Notifications::create(
//                            $this->c_created_user_id,
//                            'Missed Call (' . $this->getSourceName() . ')',
//                            'Missed Call (' . $this->getSourceName() . ')  from ' . $this->c_from . ' to ' . $this->c_to . ' <br>Lead ID: ' . $this->c_lead_id,
//                            Notifications::TYPE_WARNING,
//                            true);
//                        Notifications::socket($this->c_created_user_id, null, 'getNewNotification', [], true);
//                    }
//                }

                if ($lead = $this->cLead2) {
                    if($lead->l_call_status_id == Lead::CALL_STATUS_QUEUE) {
                        $lead->l_call_status_id = Lead::CALL_STATUS_READY;
                        if(!$lead->save()) {
                            Yii::error(VarDumper::dumpAsString($lead->errors), 'Call:afterSave:Lead2:update');
                        }
                    }
                }

                /*if($this->cLead && $this->cLead->employee_id && $this->c_created_user_id !== $this->cLead->employee_id) {
                    Notifications::create($this->c_created_user_id, 'On your Lead Missed Call ('.$this->getSourceName().')  from ' . $this->c_from . ' to ' . $this->c_to . ' <br>Lead ID: ' . $this->c_lead_id , Notifications::TYPE_WARNING, true);
                    Notifications::socket($this->c_created_user_id, null, 'getNewNotification', [], true);
                }*/



                //Yii::info(VarDumper::dumpAsString($this->attributes), 'info\Call:afterSave:createNewLead');
                //$this->createNewLead();
            }



            //Yii::info(VarDumper::dumpAsString(['changedAttributes' => $changedAttributes, 'Call' => $this->attributes, 'Lead' => $lead->attributes]), 'info\Call:afterSave');

            if($this->isInProgress() && $this->isIn() && ( $this->c_lead_id || $this->c_case_id ) && isset($changedAttributes['c_call_status'])
                && ($changedAttributes['c_call_status'] === self::CALL_STATUS_RINGING || $changedAttributes['c_call_status'] === self::CALL_STATUS_QUEUE)) {

                if($this->c_lead_id && (int) $this->c_dep_id === Department::DEPARTMENT_SALES) {
                    $lead = $this->cLead2;

                    if ($lead && !$lead->employee_id && $this->c_created_user_id && $lead->status === Lead::STATUS_PENDING) {
                        Yii::info(VarDumper::dumpAsString(['changedAttributes' => $changedAttributes, 'Call' => $this->attributes, 'Lead' => $lead->attributes]), 'info\Call:Lead:afterSave');
                        $lead->employee_id = $this->c_created_user_id;
                        $lead->status = Lead::STATUS_PROCESSING;
                        // $lead->l_call_status_id = Lead::CALL_STATUS_PROCESS;
                        $lead->l_answered = true;
                        if ($lead->save()) {
                            $host = \Yii::$app->params['url_address'] ?? '';
                            Notifications::create($lead->employee_id, 'AutoCreated new Lead (' . $lead->id . ')', 'A new lead (' . $lead->id . ') has been created for you. Call Id: ' . $this->c_id, Notifications::TYPE_SUCCESS, true);
                            Notifications::socket($lead->employee_id, null, 'getNewNotification', [], true);
                            Notifications::socket($lead->employee_id, null, 'openUrl', ['url' => $host . '/lead/view/' . $lead->gid], false);
                        } else {
                            Yii::error(VarDumper::dumpAsString($lead->errors), 'Call:afterSave:Lead:update');
                        }
                    }
                }


                if($this->c_case_id && ((int) $this->c_dep_id === Department::DEPARTMENT_EXCHANGE || (int) $this->c_dep_id === Department::DEPARTMENT_SUPPORT)) {
                    $case = $this->cCase;

                    if ($case && !$case->cs_user_id && $this->c_created_user_id && $case->isPending()) {
                        Yii::info(VarDumper::dumpAsString(['changedAttributes' => $changedAttributes, 'Call' => $this->attributes, 'Case' => $case->attributes]), 'info\Call:Case:afterSave');
                        $case->cs_user_id = $this->c_created_user_id;
                        //$case->processing($this->c_created_user_id);
                        $case->cs_status = CasesStatus::STATUS_PROCESSING;

                        if ($case->save()) {
                            $host = \Yii::$app->params['url_address'] ?? '';
                            Notifications::create($case->cs_user_id, 'AutoCreated new Case (' . $case->cs_id . ')', 'A new Case (' . $case->cs_id . ') has been created for you. Call Id: ' . $this->c_id, Notifications::TYPE_SUCCESS, true);
                            Notifications::socket($case->cs_user_id, null, 'getNewNotification', [], true);
                            Notifications::socket($case->cs_user_id, null, 'openUrl', ['url' => $host . '/cases/view/' . $case->cs_gid], false);
                        } else {
                            Yii::error(VarDumper::dumpAsString($case->errors), 'Call:afterSave:Case:update');
                        }
                    }
                }

            }




        }

        if (($insert && $this->c_created_user_id) || (isset($changedAttributes['c_call_status']) && $this->c_created_user_id))  {

            Notifications::socket($this->c_created_user_id, $this->c_lead_id, 'callUpdate', ['id' => $this->c_id, 'status' => $this->c_call_status, 'duration' => $this->c_call_duration, 'snr' => $this->c_sequence_number], true);

            /*
            if($this->c_call_type_id === self::CALL_TYPE_OUT && NULL === $this->c_parent_call_sid) {
                Notifications::socket($this->c_created_user_id, $this->c_lead_id, 'callUpdate', ['id' => $this->c_id, 'status' => $this->c_call_status, 'duration' => $this->c_call_duration, 'snr' => $this->c_sequence_number], true);
            }

            if($this->c_call_type_id === self::CALL_TYPE_IN && $this->c_parent_call_sid) {
                Notifications::socket($this->c_created_user_id, $this->c_lead_id, 'incomingCall', ['id' => $this->c_id, 'status' => $this->c_call_status, 'duration' => $this->c_call_duration, 'snr' => $this->c_sequence_number], true);
            }*/

        }

        if($this->c_call_type_id === self::CALL_TYPE_OUT && $this->c_lead_id && $this->cLead) {
            $this->cLead->updateLastAction();
        }

        Notifications::pingUserMap();
    }


    /**
     * @return int|null
     */
    /*protected function createNewLead(): ?int
    {
        $lead = new Lead2();

        $clientPhone = ClientPhone::find()->where(['phone' => $this->c_from])->orderBy(['id' => SORT_DESC])->limit(1)->one();

        if($clientPhone) {
            $client = $clientPhone->client;
        } else {
            $client = new Client();
            $client->first_name = 'ClientName';
            $client->created = date('Y-m-d H:i:s');

            if($client->save()) {
                $clientPhone = new ClientPhone();
                $clientPhone->phone = $this->c_from;
                $clientPhone->client_id = $client->id;
                $clientPhone->comments = 'incoming';
                if (!$clientPhone->save()) {
                    Yii::error(VarDumper::dumpAsString($clientPhone->errors), 'Model:Call:createNewLead:ClientPhone:save');
                }
            }
        }

        if($client) {

            $lead->status = Lead::STATUS_PENDING;
            $lead->employee_id = $this->c_created_user_id;
            $lead->client_id = $client->id;
            $lead->project_id = $this->c_project_id;

            $source = Source::find()->select('id')->where(['phone_number' => $this->c_to])->limit(1)->one();

            if(!$source) {
                $source = Source::find()->select('id')->where(['project_id' => $lead->project_id, 'default' => true])->one();
            }

            if($source) {
                $lead->source_id = $source->id;
            }

            if ($lead->save()) {
                self::updateAll(['c_lead_id' => $lead->id], ['c_id' => $this->c_id]);

                if($lead->employee_id) {
                    $task = Task::find()->where(['t_key' => Task::TYPE_MISSED_CALL])->limit(1)->one();

                    if ($task) {
                        $lt = new LeadTask();
                        $lt->lt_lead_id = $lead->id;
                        $lt->lt_task_id = $task->t_id;
                        $lt->lt_user_id = $lead->employee_id;
                        $lt->lt_date = date('Y-m-d');
                        if (!$lt->save()) {
                            Yii::error(VarDumper::dumpAsString($lt->errors), 'Model:Call:createNewLead:LeadTask:save');
                        }
                    }
                }

            } else {
                Yii::error(VarDumper::dumpAsString($lead->errors), 'Model:Call:createNewLead:Lead2:save');
            }
        }

        return $lead ? $lead->id : null;
    }*/


    /**
     * @param Call $call
     * @param int $user_id
     * @return bool
     */
    public static function applyCallToAgent(Call $call, int $user_id): bool
    {
        try {
            if ($call) {

                /*if ($call->c_created_user_id) {
                    return false;
                }*/

                if (!$call->isStatusQueue()) {
                    \Yii::warning('Error: Call ('.$call->c_call_status.') not in status QUEUE: ' . $call->c_id. ',  User: ' . $user_id, 'Call:applyCallToAgent:callRedirect');
                    return false;
                }

                $call->c_call_status = self::CALL_STATUS_RINGING;
                $call->setStatusByTwilioStatus($call->c_call_status);

                if($call->c_created_user_id && (int) $call->c_created_user_id !== $user_id) {
                    $call->c_source_type_id = self::SOURCE_REDIRECT_CALL;

                    $user = Employee::findOne($user_id);

                    Notifications::create(
                        $call->c_created_user_id,
                        'Missed Call (' . $call->getSourceName() . ')',
                        'Missed Call (' . $call->getSourceName() . ')  from ' . $call->c_from . ' to ' . $call->c_to . '. Taken by Agent: ' . ($user ? Html::encode($user->username) : '-'),
                        Notifications::TYPE_WARNING,
                        true);

                    Notifications::socket($call->c_created_user_id, null, 'getNewNotification', [], true);

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

                $call->update();


                $agent = 'seller' . $user_id;
                $res = \Yii::$app->communication->callRedirect($call->c_call_sid, 'client', $call->c_from, $agent);

                if ($res && isset($res['error']) && $res['error'] === false) {
                    if (isset($res['data']['is_error']) && $res['data']['is_error'] === true) {
                        $call->c_call_status = self::CALL_STATUS_CANCELED;
                        $call->setStatusByTwilioStatus($call->c_call_status);
                        $call->c_created_user_id = null;
                        $call->update();
                        return false;
                    }

                    /*$call->c_call_status = self::CALL_STATUS_RINGING;
                    $call->c_created_user_id = $user_id;
                    $call->update();*/

                    \Yii::info(VarDumper::dumpAsString($res), 'info\Call:applyCallToAgent:callRedirect');
                    return true;
                }
                \Yii::warning('Error: ' . VarDumper::dumpAsString($res), 'Call:applyCallToAgent:callRedirect');
            }

        } catch (\Throwable $e) {
            \Yii::error(VarDumper::dumpAsString([$e->getMessage(), $e->getFile(), $e->getLine()]), 'Call:applyCallToAgent');
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
            \Yii::error(VarDumper::dumpAsString([$e->getMessage(), $e->getFile(), $e->getLine()]), 'Call:applyCallToAgent');
        }
        return false;
    }


    /**
     * @param int $agentId
     * @return bool
     */
    public static function applyHoldCallToAgent(int $agentId): bool
    {
        try {

            $callsCount = self::find()->where(['c_call_status' => self::CALL_STATUS_QUEUE])->count(); //cache(10)
            \Yii::info(VarDumper::dumpAsString($callsCount, 10, false), 'info\Call:applyHoldCallToAgent:callRedirect_$callsCount');
            if (!$callsCount) {
                return false;
            }

            $user = Employee::findOne($agentId);
            if (!$user) {
                throw new \Exception('Agent not found by id. Call:applyHoldCallToAgent:$user:' . $agentId);
            }

            if (!$user->isOnline()) {
                throw new \Exception('Agent is not isOnline Call:applyHoldCallToAgent:isOnline:$user:' . $agentId);
            }

            if (!$user->isCallStatusReady()) {
                throw new \Exception('Agent is not isCallStatusReady. Call:applyHoldCallToAgent:isCallStatusReady:$user:' . $agentId);
            }

            if (!$user->isCallFree()) {
                throw new \Exception('Agent is not isCallFree. Call:applyHoldCallToAgent:isCallFree:$user:' . $agentId);
            }

            $project_employee_access = ProjectEmployeeAccess::find()->select(['project_id'])->where(['employee_id' => $user->id])->all();
            if (!$project_employee_access) {
                throw new \Exception('Not found ProjectEmployeeAccess. Call:applyHoldCallToAgent:$project_employee_access:$user:' . $agentId);
            }

            $projectsIds = [];
            if($project_employee_access) {
                foreach ($project_employee_access AS $pea) {
                    $projectsIds[] = $pea->project_id;
                }
            }

            //$subQuery = ProjectEmployeeAccess::find()->select(['DISTINCT(project_id)'])->where(['employee_id' => $user->id]);
            $subQueryUd = UserDepartment::find()->depsByUser($user->id);

            $calls = self::find()->where(['c_call_status' => self::CALL_STATUS_QUEUE])
                //->andWhere(['IN', 'c_project_id', $subQuery])
                ->andWhere(['c_project_id' => $projectsIds])
                ->andWhere(['IN', 'c_dep_id', $subQueryUd])
                ->orderBy(['c_id' => SORT_ASC])
                ->limit(5)
                ->all();

            //->andWhere(['c_project_id' => $projectsIds])

            if ($calls) {
                foreach ($calls as $call) {
                    $isCalled = self::applyCallToAgent($call, $user->id);
                    if($isCalled) {
                        return true;
                    }
                }
            }
        } catch (\Throwable $e) {
            \Yii::warning(VarDumper::dumpAsString([$e->getMessage(), $e->getFile(), $e->getLine()], 10, false), 'Call:applyHoldCallToAgent');
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
                    $hoursRange = ChartTools::getHoursRange($startDate, $endDate." 23:59:59", $step = '+1 hour', $format = 'H:i:s');
                } else {
                    $hoursRange = ChartTools::getHoursRange($startDate, $endDate." 23:59:59", $step = '+1 hour', $format = 'Y-m-d H:i:s');
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
                        case self::CALL_STATUS_COMPLETED :
                            $completed++;
                            $cc_Duration = $cc_Duration + $callItem->c_call_duration;
                            $cc_TotalPrice = $cc_TotalPrice + $callItem->c_price;
                            break;
                        case self::CALL_STATUS_NO_ANSWER :
                            $noAnswer++;
                            break;
                        case self::CALL_STATUS_BUSY :
                            $busy++;
                            break;
                        case self::CALL_STATUS_CANCELED :
                            $canceled++;
                            break;
                    }
                }
            }
            $item['time'] = $timeSignature;
            $item['weeksInterval'] = (count($weekInterval) == 2) ? $EndPoint : null;
            $item['completed'] = $completed;
            $item['no-answer'] = $noAnswer;
            $item['busy'] = $busy;
            $item['canceled'] = $canceled;
            $item['cc_Duration'] = $cc_Duration;
            $item['cc_TotalPrice'] = round($cc_TotalPrice, 2);

            array_push($callStats, $item);
            $completed = $noAnswer = $busy = $canceled = 0;
            $cc_Duration = $cc_TotalPrice= 0;
        }
        return $callStats;
    }

    /**
     * Returns client TimeZone 
     * 
     * @param array $callData
     * 
     * @return string
     */
    public static function getClientTime(array $callData): ?string
    {
        $country = $callData['FromCountry'] ?? '';
        $city = $callData['FromCity'] ?? '';
        $state = $callData['FromState'] ?? '';

        if (empty($country)) return null;

        $region = !empty($state) ? $state : (!empty($city) ? $city : '');

        $timezone = geoip_time_zone_by_country_and_region($country, $region);
        
        $timezone = ($timezone == false) ? (self::getTimezoneByCountryCode($country) ?? false) : $timezone;

        if ($timezone === false) return null;

        $date = new DateTime('now', new DateTimeZone($timezone));

        return $date->format('P');
    }

    /**
     * @param string $code CountryCode;
     * 
     * @return null:string
     * 
     * @throws \Exception
     */
    private static function getTimezoneByCountryCode(string $code): ?string
    {
        if (empty($code)) throw new \Exception('Country code is empty');

        $timezone = null;

        $countriesTimeZone = DateTimeZone::listIdentifiers(DateTimeZone::PER_COUNTRY, $code);

        $countTimeZones = count($countriesTimeZone);
        
        if ($countTimeZones == 3) $timezone = $countriesTimeZone[1];
        elseif ($countTimeZones < 3) $timezone = $countTimeZones[0] ?? null;

        return $timezone;
    }

    /**
     * Return Country Name by ISO Country Code
     * 
     * @param string $countryCode
     * 
     * @return string|null
     */
    public static function getDisplayRegion(string $countryCode): ?string
    {
        if (empty($countryCode)) return null;

        return Locale::getDisplayRegion("-$countryCode", 'en');
    }

    /**
     * @return bool
     */
    public function isRinging(): bool
    {
        return $this->c_call_status === self::CALL_STATUS_RINGING;
    }

    /**
     * @return bool
     */
    public function isInProgress(): bool
    {
        return $this->c_call_status === self::CALL_STATUS_IN_PROGRESS;
    }

    /**
     * @return bool
     */
    public function isIvr(): bool
    {
        return $this->c_call_status === self::CALL_STATUS_IVR;
    }

    /**
     * @return bool
     */
    public function isQueue(): bool
    {
        return $this->c_call_status === self::CALL_STATUS_QUEUE;
    }

    /**
     * @return bool
     */
    public function isBusy(): bool
    {
        return $this->c_call_status === self::CALL_STATUS_BUSY;
    }

    /**
     * @return bool
     */
    public function isCanceled(): bool
    {
        return $this->c_call_status === self::CALL_STATUS_CANCELED;
    }

    /**
     * @return bool
     */
    public function isCompleted(): bool
    {
        return $this->c_call_status === self::CALL_STATUS_COMPLETED;
    }

    /**
     * @return bool
     */
    public function isNoAnswer(): bool
    {
        return $this->c_call_status === self::CALL_STATUS_NO_ANSWER;
    }

    /**
     * @return bool
     */
    public function isFailed(): bool
    {
        return $this->c_call_status === self::CALL_STATUS_FAILED;
    }

    /**
     * @return bool
     */
    public function isIn(): bool
    {
        return $this->c_call_type_id === self::CALL_TYPE_IN;
    }

    /**
     * @return bool
     */
    public function isOut(): bool
    {
        return $this->c_call_type_id === self::CALL_TYPE_OUT;
    }


    /**
     * @param string $statusName
     * @return int|null
     */
    public static function getStatusByTwilioStatus(string $statusName): ?int
    {
        $statusId = null;
        switch ($statusName) {
            case self::CALL_STATUS_IVR:
                $statusId = self::STATUS_IVR;
                break;
            case self::CALL_STATUS_QUEUE:
                $statusId = self::STATUS_QUEUE;
                break;
            case self::CALL_STATUS_RINGING:
                $statusId = self::STATUS_RINGING;
                break;
            case self::CALL_STATUS_COMPLETED:
                $statusId = self::STATUS_COMPLETED;
                break;
            case self::CALL_STATUS_CANCELED:
                $statusId = self::STATUS_CANCELED;
                break;
            case self::CALL_STATUS_FAILED:
                $statusId = self::STATUS_FAILED;
                break;
            case self::CALL_STATUS_BUSY:
                $statusId = self::STATUS_BUSY;
                break;
            case self::CALL_STATUS_IN_PROGRESS:
                $statusId = self::STATUS_IN_PROGRESS;
                break;
            case self::CALL_STATUS_NO_ANSWER:
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
     * @return bool
     */
    public function isStatusQueue(): bool
    {
        return $this->c_status_id === self::STATUS_QUEUE;
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
        return $this->c_status_id === self::STATUS_COMPLETED;
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



}
