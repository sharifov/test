<?php

namespace src\forms\cases;

use src\entities\cases\Cases;
use src\model\clientChat\entity\ClientChat;
use src\model\clientChatCase\entity\ClientChatCase;
use src\model\clientChatChannel\entity\ClientChatChannel;
use yii\base\Model;

class CasesLinkChatForm extends Model
{
    public ?int $caseId = null;

    public ?int $chatId = null;

    public function rules()
    {
        return [
            [['caseId', 'chatId'], 'required'],
            [['caseId', 'chatId'], 'filter', 'filter' => 'intval'],
            ['caseId', 'exist', 'skipOnError' => true, 'targetClass' => Cases::class, 'targetAttribute' => ['caseId' => 'cs_id'], 'message' => 'Case not found'],
            ['chatId', 'exist', 'skipOnError' => true, 'targetClass' => ClientChat::class, 'targetAttribute' => ['chatId' => 'cch_id'], 'message' => 'Chat not found'],
            [['caseId'], 'checkIsNotLinked', 'skipOnError' => true,],
            [['caseId'], 'validateMatchParams', 'skipOnError' => true],
        ];
    }

    public function checkIsNotLinked()
    {
        if (ClientChatCase::find()->where(['cccs_case_id' => $this->caseId, 'cccs_chat_id' => $this->chatId])->exists()) {
            $this->addError('caseId', 'The case is already linked to the chat.');
        }
    }

    public function validateMatchParams()
    {
        $chat = ClientChat::find()
            ->select(['cch_project_id', 'ccc_dep_id', 'cch_client_id'])
            ->where(['cch_id' => $this->chatId])
            ->join('INNER JOIN', ClientChatChannel::tableName(), 'ccc_id = cch_channel_id')
            ->asArray()
            ->one();
        $case = Cases::find()->select(['cs_project_id', 'cs_dep_id', 'cs_client_id'])->where(['cs_id' => $this->caseId])->asArray()->one();

        if ($chat['cch_project_id'] !== $case['cs_project_id']) {
            $this->addError('caseId', 'The case project does not match.');
        }

        if ($chat['ccc_dep_id'] !== $case['cs_dep_id']) {
            $this->addError('caseId', 'The case department does not match.');
        }

        if ($chat['cch_client_id'] && $chat['cch_client_id'] !== $case['cs_client_id']) {
            $this->addError('caseId', 'The case client doest not match');
        }
    }
}
