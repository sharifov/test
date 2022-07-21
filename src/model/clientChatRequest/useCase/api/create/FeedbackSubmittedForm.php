<?php

namespace src\model\clientChatRequest\useCase\api\create;

use common\models\ClientChatSurvey;
use common\models\ClientChatSurveyResponse;
use src\model\clientChat\entity\ClientChat;
use Yii;
use yii\base\DynamicModel;
use yii\base\Model;
use yii\db\Transaction;

/**
 * Class FeedbackSubmittedForm
 * @package src\model\clientChatRequest\useCase\api\create
 */
class FeedbackSubmittedForm extends FeedbackFormBase
{
    public $responses;

    /**
     * @inheritdoc
     */
    public function rules(): array
    {
        return array_merge(parent::rules(), [
            ['responses', 'validateResponses'],
            ['id', 'validateRocketChatId']
        ]);
    }

    /**
     * @param $attribute
     * @param $params
     * @param $validator
     */
    public function validateRocketChatId($attribute, $params, $validator): void
    {
        if (!ClientChatSurvey::find()->where(['ccs_uid' => $this->id])->exists()) {
            $this->addError($attribute, 'feedback with current id not exists');
        }
    }

    /**
     * @param $attribute
     * @param $params
     * @param $validator
     */
    public function validateResponses($attribute, $params, $validator): void
    {
        /** @var array $rules The rules list for response item */
        $rules = [
            [['question'], 'string'],
            [['response'], function ($responseAttribute, $responseParams, $responseValidator) {
                if (!in_array(gettype($this->$responseAttribute), ['string', 'boolean', 'integer'])) {
                    $this->addError($responseAttribute, "unexpected type, available types: string, boolean, integer");
                }
            }]
        ];
        /** @var array $errors Reducing the error list */
        $errors = array_reduce($this->$attribute, function ($result, $response) use ($rules) {
            $fields = ['question' => $response['question'], 'response' => $response['response']];
            $model = DynamicModel::validateData($fields, $rules);
            if ($model->hasErrors()) {
                $result[] = $model->errors;
            }
            return $result;
        }, []);

        // If errors is not empty - add errors into parent model
        if (count($errors) > 0) {
            $this->addError($attribute, $errors);
        }
    }


    /**
     * @param ClientChat $clientChat
     * @return bool
     * @throws \yii\db\Exception
     */
    public function syncWithDb(ClientChat $clientChat): bool
    {
        /** @var Transaction $transaction */
        $transaction = \Yii::$app->db->beginTransaction();

        /** @var ClientChatSurvey $model */
        $model = ClientChatSurvey::find()->where(['ccs_uid' => $this->id])->one();

        if (is_null($model)) {
            return false;
        }

        Yii::$app->db->createCommand()
            ->delete(ClientChatSurveyResponse::tableName(), 'ccsr_client_chat_survey_id = :ccsr_client_chat_survey_id', [':ccsr_client_chat_survey_id' => $model->getPrimaryKey()])
            ->execute();

        $model->ccs_status = ClientChatSurvey::STATUS_SUBMITTED;
        $columns = ['ccsr_client_chat_survey_id', 'ccsr_question', 'ccsr_response', 'ccsr_created_dt'];
        $rows = array_map(function ($response) use ($model) {
            return [
                'ccsr_client_chat_survey_id' => $model->getPrimaryKey(),
                'ccsr_question' => $response['question'],
                'ccsr_response' => $response['response'],
                'ccsr_created_dt' => date('Y-m-d H:i:s')
            ];
        }, $this->responses);

        /** @var \yii\db\Command $query */
        $query = \Yii::$app->db->createCommand()->batchInsert(ClientChatSurveyResponse::tableName(), $columns, $rows);
        $model->update();

        if (empty($model->getErrors()) && $query->execute() > 0) {
            $transaction->commit();
            return true;
        } else {
            $transaction->rollBack();
            return false;
        }
    }
}
