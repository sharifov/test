<?php

namespace src\model\clientChatRequest\useCase\api\create;

use common\models\ClientChatSurvey;
use src\model\clientChat\entity\ClientChat;

/**
 * Class FeedbackRejectedForm
 * @package src\model\clientChatRequest\useCase\api\create
 */
class FeedbackRejectedForm extends FeedbackFormBase
{

    /**
     * @inheritdoc
     */
    public function rules(): array
    {
        return array_merge(parent::rules(), [
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
     * @param ClientChat $clientChat
     * @return bool
     */
    public function syncWithDb(ClientChat $clientChat): bool
    {
        /** @var ClientChatSurvey $model */
        $model = ClientChatSurvey::find()->where('ccs_uid=:id', [':id' => $this->id])->one();
        if (is_null($model)) {
            return false;
        }
        $model->ccs_status = ClientChatSurvey::STATUS_REJECT;
        return $model->save();
    }
}
