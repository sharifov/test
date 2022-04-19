<?php

namespace common\models;

use yii\db\ActiveRecord;

/**
 * Class ClientChatSurveyResponse
 * @package common\models
 *
 * @property int $ccsr_id
 * @property int $ccsr_client_chat_survey_id
 * @property string $ccsr_question
 * @property string $ccsr_response
 *
 * @property ClientChatSurvey $clientChatSurvey
 */
class ClientChatSurveyResponse extends ActiveRecord
{
    /**
     * @return string
     */
    public static function tableName()
    {
        return '{{%client_chat_survey_response}}';
    }

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['ccsr_client_chat_survey_id', 'ccsr_question', 'ccsr_response'], 'required'],
            [['ccsr_question', 'ccsr_response'], 'string'],
            [['ccsr_client_chat_survey_id'], 'integer'],
        ];
    }

    public function attributeLabels(): array
    {
        return [
          'ccsr_id' => 'ID',
          'ccsr_client_chat_survey_id' => 'Client Chat Survey ID',
          'ccsr_question' => 'Question',
          'ccsr_response' => 'Response'
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getClientChatSurvey()
    {
        return $this->hasOne(ClientChatSurvey::class, ['ccs_id' => 'ccsr_client_chat_survey_id']);
    }
}
