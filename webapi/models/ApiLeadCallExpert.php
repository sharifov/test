<?php

namespace webapi\models;

use common\models\LeadCallExpert;
use yii\base\Model;

/**
 * This is the model class for api "lead_call_expert".
 *
 * @property int $lce_id
 * @property string $lce_response_text
 * @property string $lce_response_lead_quotes
 * @property string $lce_response_dt
 * @property int $lce_expert_user_id
 * @property int $lce_status_id
 * @property string $lce_expert_username
 */
class ApiLeadCallExpert extends Model
{
    public $lce_id;
    public $lce_response_text;
    public $lce_response_lead_quotes;
    public $lce_response_dt;
    public $lce_expert_user_id;
    public $lce_status_id;
    public $lce_expert_username;

    /**
     * @return string
     */
    public function formName() : string
    {
        return 'call';
    }


    /**
     * @return array
     */
    public function rules() : array
    {
        return [
            [['lce_id', 'lce_status_id'], 'required'],

            [['lce_response_text', 'lce_expert_username'], 'required', 'when' => static function(self $model) {
                return (int) $model->lce_status_id === LeadCallExpert::STATUS_DONE;
            }],

            [['lce_expert_user_id', 'lce_id', 'lce_status_id'], 'integer'],
            [['lce_status_id'], 'in', 'range' => [LeadCallExpert::STATUS_PROCESSING, LeadCallExpert::STATUS_DONE, LeadCallExpert::STATUS_CANCEL]],
            [['lce_response_text', 'lce_response_lead_quotes'], 'string'],
            [['lce_expert_username'], 'string', 'max' => 30],
            [['lce_response_lead_quotes'], 'validateJson']
        ];
    }

    /**
     * Validates JSON lead quotes.
     * This method serves as the inline validation JSON.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validateJson($attribute): void
    {
        if (!empty($this->lce_response_lead_quotes) && !$this->hasErrors()) {

            $jsonArray = @json_decode($this->lce_response_lead_quotes, true);

            if(!is_array($jsonArray)) {
                $this->addError($attribute, 'Invalid JSON format lce_response_lead_quotes field');
            }
        }
    }

    /**
     * @return array
     */
    public function attributeLabels() : array
    {
        return [
            'lce_id' => 'ID',
            'lce_response_text' => 'Response Text',
            'lce_response_lead_quotes' => 'Response Lead Quotes',
            'lce_response_dt' => 'Response Dt',
            'lce_expert_user_id' => 'Expert User ID',
            'lce_expert_username' => 'Expert Username',
        ];
    }

}
