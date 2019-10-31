<?php

namespace common\models;

use common\components\jobs\QuickSearchInitPriceJob;
use common\components\jobs\UpdateLeadBOJob;
use common\models\local\LeadLogMessage;
use sales\services\lead\qcall\CalculateDateService;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\helpers\VarDumper;

/**
 * This is the model class for table "leads".
 *
 * @property int $id
 * @property int $client_id
 * @property int $employee_id
 * @property int $status
 * @property string $uid
 * @property int $project_id
 * @property int $source_id
 * @property string $trip_type
 * @property string $cabin
 * @property int $adults
 * @property int $children
 * @property int $infants
 * @property string $notes_for_experts
 * @property string $created
 * @property string $updated
 * @property string $request_ip
 * @property string $request_ip_detail
 * @property string $offset_gmt
 * @property string $snooze_for
 * @property int $rating
 * @property boolean $called_expert
 * @property string $discount_id
 * @property int $bo_flight_id
 * @property string $additional_information
 * @property bool $l_answered
 * @property int $clone_id
 * @property string $description
 * @property double $final_profit
 * @property string $tips
 * @property string $gid
 * @property double $agents_processing_fee
 * @property int $l_call_status_id
 * @property string $l_pending_delay_dt
 * @property string $l_client_first_name
 * @property string $l_client_last_name
 * @property string $l_client_phone
 * @property string $l_client_email
 * @property string $l_client_lang
 * @property string $l_client_ua
 * @property string $l_request_hash
 * @property int $l_duplicate_lead_id
 * @property double $l_init_price
 * @property string $l_last_action_dt
 * @property int $l_dep_id
 *
 * @property Call[] $calls
 * @property Email[] $emails
 * @property LeadCallExpert[] $leadCallExperts
 * @property LeadChecklist[] $leadChecklists
 * @property LeadFlightSegment[] $leadFlightSegments
 * @property LeadFlow[] $leadFlows
 * @property LeadLog[] $leadLogs
 * @property LeadPreferences[] $leadPreferences
 * @property LeadTask[] $leadTasks
 * @property Client $client
 * @property Lead2 $clone
 * @property Lead2[] $lead2s
 * @property Employee $employee
 * @property Lead2 $lDuplicateLead
 * @property Lead2[] $lead2s0
 * @property Department $lDep
 * @property Project $project
 * @property Sources $source
 * @property Note[] $notes
 * @property ProfitSplit[] $profitSplits
 * @property Employee[] $psUsers
 * @property Quote[] $quotes
 * @property Reason[] $reasons
 * @property Sms[] $sms
 * @property TipsSplit[] $tipsSplits
 * @property Employee[] $tsUsers
 * @property UserConnection[] $userConnections
 * @property LeadQcall $leadQcall
 *
 * @property LeadFlow $lastLeadFlow
 */
class Lead2 extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'leads';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['client_id', 'employee_id', 'status', 'project_id', 'source_id', 'rating', 'bo_flight_id', 'clone_id', 'l_call_status_id', 'l_duplicate_lead_id', 'l_dep_id'], 'integer'],
            [['adults', 'children', 'infants'], 'integer', 'max' => 9],
            [['notes_for_experts', 'request_ip_detail', 'additional_information', 'l_client_ua'], 'string'],
            [['created', 'updated', 'snooze_for', 'l_pending_delay_dt', 'l_last_action_dt'], 'safe'],
            [['final_profit', 'tips', 'agents_processing_fee', 'l_init_price'], 'number'],
            [['uid', 'request_ip', 'offset_gmt', 'discount_id', 'description'], 'string', 'max' => 255],
            [['trip_type'], 'string', 'max' => 2],
            [['cabin'], 'string', 'max' => 1],
            [['gid', 'l_request_hash'], 'string', 'max' => 32],
            [['l_client_first_name', 'l_client_last_name'], 'string', 'max' => 50],
            [['l_client_phone'], 'string', 'max' => 20],
            [['l_client_email'], 'string', 'max' => 160],
            [['l_client_lang'], 'string', 'max' => 5],
            [['gid'], 'unique'],
            [['l_answered', 'called_expert'], 'boolean'],
            [['client_id'], 'exist', 'skipOnError' => true, 'targetClass' => Client::class, 'targetAttribute' => ['client_id' => 'id']],
            [['clone_id'], 'exist', 'skipOnError' => true, 'targetClass' => self::class, 'targetAttribute' => ['clone_id' => 'id']],
            [['employee_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['employee_id' => 'id']],
            [['l_duplicate_lead_id'], 'exist', 'skipOnError' => true, 'targetClass' => self::class, 'targetAttribute' => ['l_duplicate_lead_id' => 'id']],
            [['project_id'], 'exist', 'skipOnError' => true, 'targetClass' => Project::class, 'targetAttribute' => ['project_id' => 'id']],
            [['source_id'], 'exist', 'skipOnError' => true, 'targetClass' => Sources::class, 'targetAttribute' => ['source_id' => 'id']],
            [['l_dep_id'], 'exist', 'skipOnError' => true, 'targetClass' => Department::class, 'targetAttribute' => ['l_dep_id' => 'dep_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'client_id' => 'Client ID',
            'employee_id' => 'Employee ID',
            'status' => 'Status',
            'uid' => 'Uid',
            'project_id' => 'Project ID',
            'source_id' => 'Source ID',
            'trip_type' => 'Trip Type',
            'cabin' => 'Cabin',
            'adults' => 'Adults',
            'children' => 'Children',
            'infants' => 'Infants',
            'notes_for_experts' => 'Notes For Experts',
            'created' => 'Created',
            'updated' => 'Updated',
            'request_ip' => 'Request Ip',
            'request_ip_detail' => 'Request Ip Detail',
            'offset_gmt' => 'Offset Gmt',
            'snooze_for' => 'Snooze For',
            'rating' => 'Rating',
            'called_expert' => 'Called Expert',
            'discount_id' => 'Discount ID',
            'bo_flight_id' => 'Bo Flight ID',
            'additional_information' => 'Additional Information',
            'l_answered' => 'Answered',
            'clone_id' => 'Clone ID',
            'description' => 'Description',
            'final_profit' => 'Final Profit',
            'tips' => 'Tips',
            'gid' => 'Gid',
            'agents_processing_fee' => 'Agents Processing Fee',
            'l_call_status_id' => 'Call Status ID',
            'l_pending_delay_dt' => 'Pending Delay Dt',
            'l_client_first_name' => 'Client First Name',
            'l_client_last_name' => 'Client Last Name',
            'l_client_phone' => 'Client Phone',
            'l_client_email' => 'Client Email',
            'l_client_lang' => 'Client Lang',
            'l_client_ua' => 'Client Ua',
            'l_request_hash' => 'Request Hash',
            'l_duplicate_lead_id' => 'Duplicate Lead ID',
            'l_init_price' => 'Init Price',
            'l_last_action_dt' => 'Last Action Dt',
            'l_dep_id' => 'Department ID',
        ];
    }

    public function behaviors(): array
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created', 'updated', 'l_last_action_dt'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['updated', 'l_last_action_dt'],
                ],
                'value' => date('Y-m-d H:i:s') //new Expression('NOW()'),
            ],
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLDep()
    {
        return $this->hasOne(Department::class, ['dep_id' => 'l_dep_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCalls()
    {
        return $this->hasMany(Call::class, ['c_lead_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEmails()
    {
        return $this->hasMany(Email::class, ['e_lead_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLeadCallExperts()
    {
        return $this->hasMany(LeadCallExpert::class, ['lce_lead_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLeadChecklists()
    {
        return $this->hasMany(LeadChecklist::class, ['lc_lead_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLeadFlightSegments()
    {
        return $this->hasMany(LeadFlightSegment::class, ['lead_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLeadFlows()
    {
        return $this->hasMany(LeadFlow::class, ['lead_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLeadLogs()
    {
        return $this->hasMany(LeadLog::class, ['lead_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLeadPreferences()
    {
        return $this->hasMany(LeadPreferences::class, ['lead_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLeadTasks()
    {
        return $this->hasMany(LeadTask::class, ['lt_lead_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getClient()
    {
        return $this->hasOne(Client::class, ['id' => 'client_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getClone()
    {
        return $this->hasOne(self::class, ['id' => 'clone_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLead2s()
    {
        return $this->hasMany(self::class, ['clone_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEmployee()
    {
        return $this->hasOne(Employee::class, ['id' => 'employee_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLDuplicateLead()
    {
        return $this->hasOne(self::class, ['id' => 'l_duplicate_lead_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLead2s0()
    {
        return $this->hasMany(self::class, ['l_duplicate_lead_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProject()
    {
        return $this->hasOne(Project::class, ['id' => 'project_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSource()
    {
        return $this->hasOne(Sources::class, ['id' => 'source_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getNotes()
    {
        return $this->hasMany(Note::class, ['lead_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProfitSplits()
    {
        return $this->hasMany(ProfitSplit::class, ['ps_lead_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPsUsers()
    {
        return $this->hasMany(Employee::class, ['id' => 'ps_user_id'])->viaTable('profit_split', ['ps_lead_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getQuotes()
    {
        return $this->hasMany(Quote::class, ['lead_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getReasons()
    {
        return $this->hasMany(Reason::class, ['lead_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSms()
    {
        return $this->hasMany(Sms::class, ['s_lead_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTipsSplits()
    {
        return $this->hasMany(TipsSplit::class, ['ts_lead_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTsUsers()
    {
        return $this->hasMany(Employee::class, ['id' => 'ts_user_id'])->viaTable('tips_split', ['ts_lead_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserConnections()
    {
        return $this->hasMany(UserConnection::class, ['uc_lead_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLeadQcall(): ActiveQuery
    {
        return $this->hasOne(LeadQcall::class, ['lqc_lead_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLastLeadFlow(): ActiveQuery
    {
        return $this->hasOne(LeadFlow::class, ['lead_id' => 'id'])->orderBy([LeadFlow::tableName() . '.id' => SORT_DESC])->limit(1);
    }

    /**
     * {@inheritdoc}
     * @return LeadsQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new LeadsQuery(get_called_class());
    }


    /**
     * @param bool $insert
     * @return bool
     */
    public function beforeSave($insert): bool
    {
        if (parent::beforeSave($insert)) {

            if($insert) {
                if (!$this->uid) {
                    $this->uid = uniqid();
                }

                if (!$this->gid) {
                    $this->gid = md5(uniqid('', true));
                }
            }
            return true;
        }
        return false;
    }

    /**
     * @return bool
     */
    public function createOrUpdateQCall(): bool
    {

        if ($this->lastLeadFlow) {
            $callCount = (int) $this->lastLeadFlow->lf_out_calls;
        } else {
            $callCount = 0;
        }

        $qcConfig = QcallConfig::getByStatusCall($this->status, $callCount);

        // Yii::info(VarDumper::dumpAsString(['lead_id' => $this->id, 'status' => $this->status, 'callCount' => $callCount, 'qcConfig' => $qcConfig ? $qcConfig->attributes : null]), 'info\createOrUpdateQCall');

        $lq = $this->leadQcall;

        if ($qcConfig) {
            if (!$lq) {
                $lq = new LeadQcall();
                $lq->lqc_lead_id = $this->id;
                $lq->lqc_weight = $this->project_id * 10;
            }

            $date = (new CalculateDateService())->calculate(
                $qcConfig->qc_client_time_enable,
                $this->offset_gmt,
                $qcConfig->qc_time_from,
                $qcConfig->qc_time_to
            );

            $lq->lqc_dt_from = $date->from;
            $lq->lqc_dt_to = $date->to;

            if (!$lq->save()) {
                Yii::error(VarDumper::dumpAsString($lq->errors), 'Lead2:createOrUpdateQCall:LeadQcall:save');
                return true;
            }
        } else {
            if ($lq) {
                try {
                    $lq->delete();
                } catch (\Throwable $throwable) {
                    Yii::error($throwable->getMessage(), 'Lead2:createOrUpdateQCall:Throwable');
                }
            }
        }
        return false;
    }

    /**
     * @param bool $insert
     * @param array $changedAttributes
     */
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        if ($insert) {
            LeadFlow::addStateFlow2($this, $insert);

            //if ($this->scenario === self::SCENARIO_API) {
                $this->createOrUpdateQCall();
            //}

        } else {
            if (isset($changedAttributes['status']) && $changedAttributes['status'] !== $this->status) {
                LeadFlow::addStateFlow2($this, $insert);
            }
        }


        if(($this->status === Lead::STATUS_PROCESSING && isset($changedAttributes['employee_id'])) || (isset($changedAttributes['l_answered']) && $changedAttributes['l_answered'] !== $this->l_answered)) {
            LeadTask::deleteUnnecessaryTasks($this->id);

            if($this->l_answered) {
                $taskType = Task::CAT_ANSWERED_PROCESS;
            } else {
                $taskType = Task::CAT_NOT_ANSWERED_PROCESS;
            }

            LeadTask::createTaskList($this->id, $this->employee_id, 1, '', $taskType);
            LeadTask::createTaskList($this->id, $this->employee_id, 2, '', $taskType);
            LeadTask::createTaskList($this->id, $this->employee_id, 3, '', $taskType);
        }

    }


    /**
     * @param string $phoneNumber
     * @param int $project_id
     * @param bool $sql
     * @return array|Lead2|string|null
     */
    public static function findLastLeadByClientPhone(string $phoneNumber = '', ?int $project_id = null, bool $sql = false)
    {
        $query = self::find()->innerJoinWith(['client.clientPhones'])
            ->where(['client_phone.phone' => $phoneNumber])
            ->andWhere(['<>', 'leads.status', Lead::STATUS_TRASH])
            ->orderBy(['leads.id' => SORT_DESC])
            ->limit(1);

        if($project_id) {
            $query->andWhere(['leads.project_id' => $project_id]);
        }

        return $sql ? $query->createCommand()->getRawSql() : $query->one();
    }


	/**
	 * @param string $phoneNumber
	 * @param int $project_id
	 * @param int $source_id
	 * @param $gmt
	 * @return Lead2
	 */
    public static function createNewLeadByPhone(string $phoneNumber = '', int $project_id = 0, int $source_id = 0, $gmt): Lead2
    {
        $lead = new self();
        $clientPhone = ClientPhone::find()->where(['phone' => $phoneNumber])->orderBy(['id' => SORT_DESC])->limit(1)->one();

        if($clientPhone) {
            $client = $clientPhone->client;
        } else {
            $client = new Client();
            $client->first_name = 'ClientName';
            $client->created = date('Y-m-d H:i:s');

            if($client->save()) {
                $clientPhone = new ClientPhone();
                $clientPhone->phone = $phoneNumber;
                $clientPhone->client_id = $client->id;
                $clientPhone->comments = 'incoming';
                if (!$clientPhone->save()) {
                    Yii::error(VarDumper::dumpAsString($clientPhone->errors), 'Model:Lead2:createNewLeadByPhone:ClientPhone:save');
                }
            }
        }

        if($client) {

            $lead->status = Lead::STATUS_PENDING;
            //$lead->employee_id = $this->c_created_user_id;
            $lead->client_id = $client->id;
            $lead->project_id = $project_id;
            $lead->source_id = $source_id;
            $lead->l_call_status_id = Lead::CALL_STATUS_QUEUE;
            $lead->offset_gmt = $gmt;
            $source = null;

			if ($source_id) {
				$source = Sources::findOne(['id' => $lead->source_id]);
			}

			if (!$source) {
				$source = Sources::find()->select('id')->where(['project_id' => $lead->project_id, 'default' => true])->one();
			}

            if($source) {
                $lead->source_id = $source->id;
            }

            if ($lead->save()) {
                /*self::updateAll(['c_lead_id' => $lead->id], ['c_id' => $this->c_id]);

                if($lead->employee_id) {
                    $task = Task::find()->where(['t_key' => Task::TYPE_MISSED_CALL])->limit(1)->one();

                    if ($task) {
                        $lt = new LeadTask();
                        $lt->lt_lead_id = $lead->id;
                        $lt->lt_task_id = $task->t_id;
                        $lt->lt_user_id = $lead->employee_id;
                        $lt->lt_date = date('Y-m-d');
                        if (!$lt->save()) {
                            Yii::error(VarDumper::dumpAsString($lt->errors), 'Model:Lead:createNewLeadByPhone:LeadTask:save');
                        }
                    }
                }*/

            } else {
                Yii::error(VarDumper::dumpAsString($lead->errors), 'Model:Lead2:createNewLeadByPhone:Lead2:save');
            }
        }

        return $lead;
    }

    /**
     * @return int
     */
    public function updateLastAction() : int
    {
        return self::updateAll(['l_last_action_dt' => date('Y-m-d H:i:s')], ['id' => $this->id]);
    }

}
