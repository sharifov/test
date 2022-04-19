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
    const STATUS_PENDING = 1;
    const STATUS_SUBMITTED = 2;
    const STATUS_REJECT = 3;

    const TYPE_FULLSCREEN = 'fullscreen';
    const TYPE_INLINE = 'inline';
    const TYPE_STICKY = 'sticky';
    const TYPE_QUESTIONS = 'questions';

    const TRIGGER_SOURCE_AGENT = 'agent';
    const TRIGGER_SOURCE_CHAT_CLOSE = 'chat-close';
    const TRIGGER_SOURCE_BOT = 'bot';

    const STATUS_LIST = [
        self::STATUS_PENDING => 'PENDING',
        self::STATUS_SUBMITTED => 'SUBMITTED',
        self::STATUS_REJECT => 'REJECT'
    ];

    const TYPE_LIST = [
        self::TYPE_FULLSCREEN => 'FULLSCREEN',
        self::TYPE_INLINE => 'INLINE',
        self::TYPE_STICKY => 'STICKY',
        self::TYPE_QUESTIONS => 'QUESTIONS'
    ];

    const TRIGGER_SOURCE_LIST = [
        self::TRIGGER_SOURCE_AGENT => 'Agent',
        self::TRIGGER_SOURCE_CHAT_CLOSE => 'Chat closing',
        self::TRIGGER_SOURCE_BOT => 'Bot'
    ];

    /**
     * @return string
     */
    public static function tableName()
    {
        return '{{%client_chat_survey}}';
    }

    /**
     * @param int $id
     * @return string
     */
    public static function statusName(int $id): string
    {
        return self::STATUS_LIST[$id];
    }

    /**
     * @param string $identity
     * @return string
     */
    public static function typeName(string $identity): string
    {
        return self::TYPE_LIST[$identity];
    }

    /**
     * @param string $identity
     * @return string
     */
    public static function triggerSourceName(string $identity): string
    {
        return self::TRIGGER_SOURCE_LIST[$identity];
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
