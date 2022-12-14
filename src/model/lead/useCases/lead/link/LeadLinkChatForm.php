<?php

namespace src\model\lead\useCases\lead\link;

use common\models\Lead;
use src\model\clientChat\entity\ClientChat;
use src\model\clientChatLead\entity\ClientChatLead;
use yii\base\Model;

class LeadLinkChatForm extends Model
{
    public ?int $leadId = null;

    public ?int $chatId = null;

    public function rules()
    {
        return [
            [['leadId', 'chatId'], 'required'],
            [['leadId', 'chatId'], 'filter', 'filter' => 'intval'],
            ['leadId', 'exist', 'skipOnError' => true, 'targetClass' => Lead::class, 'targetAttribute' => ['leadId' => 'id'], 'message' => 'Lead not found'],
            ['chatId', 'exist', 'skipOnError' => true, 'targetClass' => ClientChat::class, 'targetAttribute' => ['chatId' => 'cch_id'], 'message' => 'Chat not found'],
            [['leadId'], 'checkIsNotLinked', 'skipOnError' => true,],
            [['leadId'], 'validateMatchParams', 'skipOnError' => true],
        ];
    }

    public function checkIsNotLinked()
    {
        if (ClientChatLead::find()->where(['ccl_lead_id' => $this->leadId, 'ccl_chat_id' => $this->chatId])->exists()) {
            $this->addError('leadId', 'The lead is already linked to the chat.');
        }
    }

    public function validateMatchParams()
    {
        $chat = ClientChat::find()
            ->select(['cch_project_id', 'cch_client_id'])
            ->where(['cch_id' => $this->chatId])
            ->asArray()
            ->one();
        $lead = Lead::find()->select(['project_id', 'client_id'])->where(['id' => $this->leadId])->asArray()->one();

        if ($chat['cch_project_id'] !== $lead['project_id']) {
            $this->addError('leadId', 'The lead project does not match.');
        }

        if ($chat['cch_client_id'] && $chat['cch_client_id'] !== $lead['client_id']) {
            $this->addError('leadId', 'The lead client doest not match');
        }
    }
}
