<?php

namespace common\models;

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
        self::STATUS_PENDING    => 'pending',
        self::STATUS_PROCESSING => 'processing',
        self::STATUS_DONE       => 'done',
        self::STATUS_CANCEL     => 'cancel',
    ];

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
        ];
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
}
