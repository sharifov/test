<?php

namespace src\model\clientChatRequest\useCase\api\create;

use common\models\ClientChatSurvey;
use common\models\Employee;
use src\model\clientChat\entity\ClientChat;

/**
 * Class FeedbackRequestedForm
 * @package src\model\clientChatRequest\useCase\api\create
 */
class FeedbackRequestedForm extends FeedbackFormBase
{
    /**
     * @return array
     */
    public function rules(): array
    {
        return array_merge(parent::rules(), [
            ['id', 'validateChatId']
        ]);
    }

    /**
     * @param $attribute
     * @param $params
     * @param $validator
     */
    public function validateChatId($attribute, $params, $validator)
    {
        if (ClientChatSurvey::find()->where(['ccs_uid' => $this->id])->exists()) {
            $this->addError($attribute, 'current `id` already been taken');
        }
    }

    /**
     * @param ClientChat $clientChat
     * @return bool
     */
    public function syncWithDb(ClientChat $clientChat): bool
    {
        $requestedByUsername = isset($this->requestedBy['username']) ? $this->requestedBy['username'] : null;
        $requestedByEmployee = !is_null($requestedByUsername)
            ? Employee::find()->where(['username' => $this->requestedBy['username']])->one()
            : null;
        $requestedForEmployee = Employee::find()->where(['username' => $this->requestedFor['username']])->one();

        $model = new ClientChatSurvey();
        $model->load([
            'ccs_uid' => $this->id,
            'ccs_client_chat_id' => $clientChat->cch_id,
            'ccs_type' => $this->type,
            'ccs_template' => $this->template,
            'ccs_trigger_source' => $this->triggerSource,
            'ccs_requested_by' => ($requestedByEmployee !== null) ? $requestedByEmployee->getPrimaryKey() : null,
            'ccs_requested_for' => $requestedForEmployee->getPrimaryKey(),
            'ccs_rc_created_dt' => date('Y-m-d H:i:s', strtotime($this->createdAt)),
            'ccs_status' => ClientChatSurvey::STATUS_PENDING
        ], '');

        return $model->save();
    }
}
