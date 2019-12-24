<?php

namespace common\models;

use common\components\BackOffice;
use common\models\query\LeadCallExpertQuery;
use Yii;

/**
 * This is the model class for table "lead_call_expert".
 *
 * @property int $lce_id
 * @property int $lce_lead_id
 * @property string $lce_request_text
 * @property string $lce_request_dt
 * @property string $lce_response_text
 * @property string $lce_response_lead_quotes
 * @property string $lce_response_dt
 * @property int $lce_status_id
 * @property int $lce_agent_user_id
 * @property int $lce_expert_user_id
 * @property string $lce_expert_username
 * @property string $lce_updated_dt
 *
 * @property Employee $lceAgentUser
 * @property Lead $lceLead
 */
class LeadCallExpert extends \yii\db\ActiveRecord
{

    public const STATUS_PENDING     = 1;
    public const STATUS_PROCESSING  = 2;
    public const STATUS_DONE        = 3;
    public const STATUS_CANCEL      = 4;

    public const STATUS_LIST = [
        self::STATUS_PENDING    => 'Pending',
        self::STATUS_PROCESSING => 'Processing',
        self::STATUS_DONE       => 'Done',
        self::STATUS_CANCEL     => 'Cancel',
    ];


    public const STATUS_LIST_LABEL = [
        self::STATUS_PENDING    => '<span class="badge badge-yellow">Pending</span>',
        self::STATUS_PROCESSING => '<span class="badge badge-warning">Processing</span>',
        self::STATUS_DONE       => '<span class="badge badge-green">Done</span>',
        self::STATUS_CANCEL     => '<span class="badge badge-danger">Cancel</span>',
    ];

    public const SCENARIO_API_UPDATE = 'api_update';


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'lead_call_expert';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['lce_lead_id', 'lce_request_text'], 'required'],
            [['lce_lead_id', 'lce_status_id', 'lce_agent_user_id', 'lce_expert_user_id'], 'integer'],
            [['lce_request_text', 'lce_response_text', 'lce_response_lead_quotes'], 'string'],
            [['lce_request_dt', 'lce_response_dt', 'lce_updated_dt'], 'safe'],
            [['lce_expert_username'], 'string', 'max' => 30],
            [['lce_agent_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['lce_agent_user_id' => 'id']],
            [['lce_lead_id'], 'exist', 'skipOnError' => true, 'targetClass' => Lead::class, 'targetAttribute' => ['lce_lead_id' => 'id']],
            [['lce_lead_id'], 'validateStatus', 'except' => self::SCENARIO_API_UPDATE],
            [['lce_lead_id'], 'validateFlights', 'except' => self::SCENARIO_API_UPDATE]
        ];
    }

    /**
     * Validates the status.
     * This method serves as the inline validation for status.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validateStatus($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $query = self::find();
            $query->where(['lce_lead_id' => $this->lce_lead_id, 'lce_status_id' => [self::STATUS_PENDING, self::STATUS_PROCESSING]]);
            /*if($this->lce_id) {
                $query->andWhere(['<>', 'lce_id', $this->lce_id]);
            }*/
            $query->limit(1);
            $call = $query->one();

            if ($call) {
                $this->addError($attribute, 'Exist Call Expert on status pending (id: '.$call->lce_id.')');
            }
        }
    }


    /**
     * Validates the flights.
     * This method serves as the inline validation for flights.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validateFlights($attribute, $params)
    {
        if (!$this->hasErrors()) {
            /*$query = LeadFlightSegment::find();
            $query->where(['lead_id' => $this->lce_lead_id]);
            $exists = $query->exists();*/

            $count = $this->lceLead->leadFlightSegmentsCount;

            if (!$count) {
                $this->addError($attribute, 'Flight Segments is empty');
            }
        }
    }


    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'lce_id' => 'ID',
            'lce_lead_id' => 'Lead ID',
            'lce_request_text' => 'Request Text',
            'lce_request_dt' => 'Request Dt',
            'lce_response_text' => 'Response Text',
            'lce_response_lead_quotes' => 'Response Lead Quotes',
            'lce_response_dt' => 'Response Dt',
            'lce_status_id' => 'Status ID',
            'lce_agent_user_id' => 'Agent User ID',
            'lce_expert_user_id' => 'Expert User ID',
            'lce_expert_username' => 'Expert Username',
            'lce_updated_dt' => 'Updated Dt',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLceAgentUser()
    {
        return $this->hasOne(Employee::class, ['id' => 'lce_agent_user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLceLead()
    {
        return $this->hasOne(Lead::class, ['id' => 'lce_lead_id']);
    }

    /**
     * {@inheritdoc}
     * @return LeadCallExpertQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new LeadCallExpertQuery(get_called_class());
    }

    /**
     * @return mixed|string
     */
    public function getStatusName()
    {
        return self::STATUS_LIST[$this->lce_status_id] ?? '-';
    }

    /**
     * @return mixed|string
     */
    public function getStatusLabel()
    {
        return self::STATUS_LIST_LABEL[$this->lce_status_id] ?? '-';
    }


    /**
     * @return bool
     */
    public function callExpert(): bool
    {
        $lead = $this->lceLead;

        if($lead) {
            $data = $lead->getLeadInformationForExpert();
            $data['call_expert'] = true;

            $call['lce_id']             = $this->lce_id;
            $call['lce_status_id']      = $this->lce_status_id;
            $call['lce_agent_user_id']  = $this->lce_agent_user_id;
            $call['lce_agent_username'] = $this->lceAgentUser ? $this->lceAgentUser->username : null;
            $call['lce_request_text']   = $this->lce_request_text;

            $data['call'] = $call;

            $data['LeadRequest']['information']['notes_for_experts'] = $this->lce_request_text;

            $result = BackOffice::sendRequest('lead/update-lead', 'POST', json_encode($data));

            if(!$lead->called_expert) {
                $lead->called_expert = true;
                $lead->save();
            }

            if ($result && isset($result['status']) && $result['status'] === 'Success') {
                return true;
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
            $this->callExpert();
        }

        if($this->lce_lead_id && $this->lceLead) {
            $this->lceLead->updateLastAction();
        }

    }


}
