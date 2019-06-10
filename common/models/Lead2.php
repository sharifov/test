<?php

namespace common\models;

use common\components\jobs\QuickSearchInitPriceJob;
use common\components\jobs\UpdateLeadBOJob;
use common\models\local\LeadLogMessage;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

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
 * @property int $l_grade
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
            [['client_id', 'employee_id', 'status', 'project_id', 'source_id', 'rating', 'bo_flight_id', 'l_grade', 'clone_id', 'l_call_status_id', 'l_duplicate_lead_id'], 'integer'],
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
            'l_grade' => 'Grade',
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
     * {@inheritdoc}
     * @return LeadsQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new LeadsQuery(get_called_class());
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
        } else {
            if (isset($changedAttributes['status']) && $changedAttributes['status'] !== $this->status) {
                LeadFlow::addStateFlow2($this, $insert);
            }
        }

    }
}
