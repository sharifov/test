<?php

namespace common\models;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * Class ClientChatSurvey
 * @package common\models
 *
 * @property int $ccs_id
 * @property string $ccs_uid
 * @property string $ccs_chat_id
 * @property string $ccs_type
 * @property string $ccs_template
 * @property string $ccs_trigger_source
 * @property int|null $ccs_requested_by
 * @property int $ccs_requested_for
 * @property int $ccs_status
 * @property string|null $ccs_created_dt
 *
 * @property Employee $requestedBy
 * @property Employee $requestedFor
 * @property ClientChatSurveyResponse[] $clientChatSurveyResponses
 */
class ClientChatSurvey extends ActiveRecord
{
    /**
     * @return string
     */
    public static function tableName()
    {
        return '{{%client_chat_survey}}';
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
                    ActiveRecord::EVENT_BEFORE_INSERT => ['ccs_created_dt'],
                ],
                'value' => date('Y-m-d H:i:s')
            ]
        ];
    }

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['ccs_uid', 'ccs_chat_id', 'ccs_type', 'ccs_template', 'ccs_trigger_source', 'ccs_requested_for', 'ccs_status'], 'required'],
            [['ccs_uid', 'ccs_chat_id', 'ccs_type', 'ccs_template', 'ccs_trigger_source'], 'string'],
            [['ccs_requested_by', 'ccs_requested_for', 'ccs_status'], 'integer'],
            [['ccs_created_dt'], 'safe']
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'ccs_id' => 'ID',
            'ccs_uid' => 'UID',
            'ccs_chat_id' => 'Chat Id',
            'ccs_type' => 'Type',
            'ccs_template' => 'Template',
            'ccs_created_dt' => 'Created Dt',
            'ccs_trigger_source' => 'Trigger Source',
            'ccs_requested_by' => 'Requested By',
            'ccs_requested_for' => 'Requested For',
            'ccs_status' => 'Status',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRequestedBy()
    {
        return $this->hasOne(Employee::class, ['id' => 'ccs_requested_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRequestedFor()
    {
        return $this->hasOne(Employee::class, ['id' => 'ccs_requested_for']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getClientChatSurveyResponses()
    {
        return $this->hasMany(ClientChatSurveyResponse::class, ['ccsr_client_chat_survey_id' => 'ccs_id']);
    }
}
