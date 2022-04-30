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
     * @param ClientChat $clientChat
     * @return bool
     */
    public function syncWithDb(ClientChat $clientChat): bool
    {
        $requestedByEmployee = Employee::find()->where(['username' => $this->requestedBy['username']])->one();
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
