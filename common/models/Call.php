<?php

namespace common\models;

use sales\entities\AggregateRoot;
use sales\entities\EventTrait;
use Yii;
use DateTime;
use common\components\ChartTools;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\VarDumper;


/**
 * This is the model class for table "call".
 *
 * @property int $c_id
 * @property string $c_call_sid
 * @property string $c_account_sid
 * @property int $c_call_type_id
 * @property string $c_from
 * @property string $c_to
 * @property string $c_sip
 * @property string $c_call_status
 * @property string $c_api_version
 * @property string $c_direction
 * @property string $c_forwarded_from
 * @property string $c_caller_name
 * @property string $c_parent_call_sid
 * @property int $c_call_duration
 * @property string $c_sip_response_code
 * @property string $c_recording_url
 * @property string $c_recording_sid
 * @property int $c_recording_duration
 * @property string $c_timestamp
 * @property string $c_uri
 * @property string $c_sequence_number
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
 *
 * @property Employee $cCreatedUser
 * @property Lead $cLead
 * @property Lead2 $cLead2
 * @property Project $cProject
 */
class Call extends \yii\db\ActiveRecord implements AggregateRoot
{
    
    use EventTrait;

    public const CALL_STATUS_QUEUE          = 'queued';
    public const CALL_STATUS_RINGING        = 'ringing';
    public const CALL_STATUS_IN_PROGRESS    = 'in-progress';
    public const CALL_STATUS_COMPLETED      = 'completed';
    public const CALL_STATUS_BUSY           = 'busy';
    public const CALL_STATUS_NO_ANSWER      = 'no-answer';
    public const CALL_STATUS_FAILED         = 'failed';
    public const CALL_STATUS_CANCELED       = 'canceled';

    public const CALL_STATUS_LIST = [
        self::CALL_STATUS_QUEUE         => 'Queued',
        self::CALL_STATUS_RINGING       => 'Ringing',
        self::CALL_STATUS_IN_PROGRESS   => 'In progress',
        self::CALL_STATUS_COMPLETED     => 'Completed',
        self::CALL_STATUS_BUSY          => 'Busy',
        self::CALL_STATUS_NO_ANSWER     => 'No answer',
        self::CALL_STATUS_FAILED        => 'Failed',
        self::CALL_STATUS_CANCELED      => 'Canceled',
    ];

    public const CALL_STATUS_LABEL_LIST = [
        self::CALL_STATUS_QUEUE         => '<span class="label label-warning"><i class="fa fa-refresh fa-spin"></i> ' . self::CALL_STATUS_LIST[self::CALL_STATUS_QUEUE] . '</span>',
        self::CALL_STATUS_RINGING       => '<span class="label label-warning"><i class="fa fa-spinner fa-spin"></i> ' . self::CALL_STATUS_LIST[self::CALL_STATUS_RINGING] . '</span>',
        self::CALL_STATUS_IN_PROGRESS   => '<span class="label label-success"><i class="fa fa-volume-control-phone"></i> ' . self::CALL_STATUS_LIST[self::CALL_STATUS_IN_PROGRESS] . '</span>',
        self::CALL_STATUS_COMPLETED     => '<span class="label label-info"><i class="fa fa-check"></i> ' . self::CALL_STATUS_LIST[self::CALL_STATUS_COMPLETED] . '</span>',
        self::CALL_STATUS_BUSY          => '<span class="label label-danger"><i class="fa fa-ban"></i> ' . self::CALL_STATUS_LIST[self::CALL_STATUS_BUSY] . '</span>',
        self::CALL_STATUS_NO_ANSWER     => '<span class="label label-danger"><i class="fa fa-times-circle"></i> ' . self::CALL_STATUS_LIST[self::CALL_STATUS_NO_ANSWER] . '</span>',
        self::CALL_STATUS_FAILED        => '<span class="label label-danger"><i class="fa fa-window-close"></i> ' . self::CALL_STATUS_LIST[self::CALL_STATUS_FAILED] . '</span>',
        self::CALL_STATUS_CANCELED      => '<span class="label label-danger"><i class="fa fa-close"></i> ' . self::CALL_STATUS_LIST[self::CALL_STATUS_CANCELED] . '</span>',
    ];

    public const CALL_STATUS_DESCRIPTION_LIST = [
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
            [['c_call_type_id', 'c_lead_id', 'c_created_user_id', 'c_com_call_id', 'c_project_id', 'c_call_duration', 'c_recording_duration'], 'integer'],
            [['c_price'], 'number'],
            [['c_is_new'], 'default', 'value' => true],
            [['c_is_new', 'c_is_deleted'], 'boolean'],
            [['c_created_dt', 'c_updated_dt'], 'safe'],
            [['c_call_sid', 'c_account_sid', 'c_parent_call_sid', 'c_recording_sid'], 'string', 'max' => 34],
            [['c_from', 'c_to', 'c_sip', 'c_forwarded_from'], 'string', 'max' => 100],
            [['c_call_status', 'c_direction'], 'string', 'max' => 15],
            [['c_api_version', 'c_sip_response_code'], 'string', 'max' => 10],
            [['c_caller_name'], 'string', 'max' => 50],
            [['c_recording_url', 'c_uri'], 'string', 'max' => 200],
            [['c_timestamp', 'c_sequence_number'], 'string', 'max' => 40],
            [['c_error_message'], 'string', 'max' => 500],
            [['c_created_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['c_created_user_id' => 'id']],
            [['c_lead_id'], 'exist', 'skipOnError' => true, 'targetClass' => Lead::class, 'targetAttribute' => ['c_lead_id' => 'id']],
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
            'c_call_sid' => 'Call Sid',
            'c_account_sid' => 'Account Sid',
            'c_call_type_id' => 'Call Type ID',
            'c_from' => 'From',
            'c_to' => 'To',
            'c_sip' => 'Sip',
            'c_call_status' => 'Call Status',
            'c_api_version' => 'Api Version',
            'c_direction' => 'Direction',
            'c_forwarded_from' => 'Forwarded From',
            'c_caller_name' => 'Caller Name',
            'c_parent_call_sid' => 'Parent Call Sid',
            'c_call_duration' => 'Call Duration',
            'c_sip_response_code' => 'Sip Response Code',
            'c_recording_url' => 'Recording Url',
            'c_recording_sid' => 'Recording Sid',
            'c_recording_duration' => 'Recording Duration',
            'c_timestamp' => 'Timestamp',
            'c_uri' => 'Uri',
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
    public function getSourceName()
    {
        return self::SOURCE_LIST[$this->c_source_type_id] ?? '-';
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
            if(isset($changedAttributes['c_call_status']) && in_array($this->c_call_status, [self::CALL_STATUS_COMPLETED, self::CALL_STATUS_BUSY, self::CALL_STATUS_NO_ANSWER], false)) {
                if($this->c_created_user_id) {
                    self::applyHoldCallToAgent($this->c_created_user_id);
                }
            }

            //Yii::info(VarDumper::dumpAsString($this->attributes), 'info\Call:afterSave');

            if($this->c_call_type_id === self::CALL_TYPE_IN && $this->c_lead_id && in_array($this->c_call_status, [self::CALL_STATUS_BUSY, self::CALL_STATUS_NO_ANSWER], false)) {

                if($this->c_created_user_id) {
                    Notifications::create($this->c_created_user_id, 'Missing Call ('.$this->getSourceName().')  from ' . $this->c_from . ' to ' . $this->c_to . ' <br>Lead ID: ' . $this->c_lead_id , Notifications::TYPE_WARNING, true);
                    Notifications::socket($this->c_created_user_id, null, 'getNewNotification', [], true);
                }

                /*if($this->cLead && $this->cLead->employee_id && $this->c_created_user_id !== $this->cLead->employee_id) {
                    Notifications::create($this->c_created_user_id, 'On your Lead Missing Call ('.$this->getSourceName().')  from ' . $this->c_from . ' to ' . $this->c_to . ' <br>Lead ID: ' . $this->c_lead_id , Notifications::TYPE_WARNING, true);
                    Notifications::socket($this->c_created_user_id, null, 'getNewNotification', [], true);
                }*/



                //Yii::info(VarDumper::dumpAsString($this->attributes), 'info\Call:afterSave:createNewLead');
                //$this->createNewLead();
            }



            //Yii::info(VarDumper::dumpAsString(['changedAttributes' => $changedAttributes, 'Call' => $this->attributes, 'Lead' => $lead->attributes]), 'info\Call:afterSave');

            if($this->c_call_status === self::CALL_STATUS_IN_PROGRESS && $this->c_call_type_id === self::CALL_TYPE_IN && $this->c_lead_id && isset($changedAttributes['c_call_status']) && $changedAttributes['c_call_status'] === self::CALL_STATUS_RINGING) {

                $lead = $this->cLead2;

                if($lead && !$lead->employee_id && $this->c_created_user_id && $lead->status === Lead::STATUS_PENDING) {
                    Yii::info(VarDumper::dumpAsString(['changedAttributes' => $changedAttributes, 'Call' => $this->attributes, 'Lead' => $lead->attributes]), 'info\Call:afterSave2');
                    $lead->employee_id = $this->c_created_user_id;
                    $lead->status = Lead::STATUS_PROCESSING;
                    $lead->l_answered = true;
                    if($lead->save()) {
                        $host = \Yii::$app->params['url_address'] ?? '';
                        Notifications::create($lead->employee_id, 'AutoCreated new Lead ('.$lead->id.')', 'A new lead ('.$lead->id.') has been created for you. Call Id: ' . $this->c_id, Notifications::TYPE_SUCCESS, true);
                        Notifications::socket($lead->employee_id, null, 'getNewNotification', [], true);
                        Notifications::socket($lead->employee_id, null, 'openUrl', ['url' => $host . '/lead/view/'. $lead->gid], false);
                    } else {
                        Yii::error(VarDumper::dumpAsString($lead->errors), 'Call:afterSave:Lead:update');
                    }
                }
            }

        }

        if (($insert && $this->c_created_user_id) || (isset($changedAttributes['c_call_status']) && $this->c_created_user_id))  {
            Notifications::socket($this->c_created_user_id, null, 'callUpdate', ['id' => $this->c_id, 'status' => $this->c_call_status, 'duration' => $this->c_call_duration, 'snr' => $this->c_sequence_number], true);
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
     * @param int $agentId
     * @return bool
     */
    public static function applyHoldCallToAgent(int $agentId): bool
    {
        try {

            $callsCount = self::find()->where(['c_call_status' => self::CALL_STATUS_QUEUE])->cache(10)->count();
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

            $project_employee_access = ProjectEmployeeAccess::find()->where(['employee_id' => $user->id])->all();
            if (!$project_employee_access) {
                throw new \Exception('Not found ProjectEmployeeAccess. Call:applyHoldCallToAgent:$project_employee_access:$user:' . $agentId);
            }

            $projectsIds = [];
            foreach ($project_employee_access AS $pea) {
                $projectsIds[] = $pea->project_id;
            }

            $calls = self::find()->where(['c_call_status' => self::CALL_STATUS_QUEUE])
                ->andWhere(['c_project_id' => $projectsIds])
                ->orderBy(['c_id' => SORT_ASC])
                ->limit(20)
                ->all();

            if (!$calls) {
                foreach ($calls as $call) {

                    if ($call->c_created_user_id) {
                        continue;
                    }

                    $call->c_created_user_id = $user->id;
                    $call->update();

                    $agent = 'seller' . $user->id;
                    $res = \Yii::$app->communication->callRedirect($call->c_call_sid, 'client', $call->c_from, $agent);

                    if ($res && isset($res['error']) && $res['error'] === false) {
                        if (isset($res['data']['is_error']) && $res['data']['is_error'] === true) {
                            $call->c_call_status = self::CALL_STATUS_CANCELED;
                            $call->c_created_user_id = null;
                            $call->update();
                            continue;
                        }

                        $call->c_call_status = self::CALL_STATUS_RINGING;
                        $call->c_created_user_id = $user->id;
                        $call->update();

                        \Yii::info(VarDumper::dumpAsString($res, 10, false), 'info\Call:applyHoldCallToAgent:callRedirect');
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
}
