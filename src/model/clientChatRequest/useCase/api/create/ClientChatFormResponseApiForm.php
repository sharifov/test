<?php

namespace src\model\clientChatRequest\useCase\api\create;

use src\model\clientChat\entity\ClientChat;
use src\model\clientChatForm\entity\ClientChatForm;
use src\model\clientChatFormResponse\entity\ClientChatFormResponse;
use yii\base\Model;

/**
 * Class ClientChatFormResponseApiForm
 *
 * @property string $event
 * @property int $eventId
 * @property array $data
 * @property string|null $rid
 */
class ClientChatFormResponseApiForm extends Model
{
    public $id;
    public $rid;
    public $createdAt;
    public $formKey;
    public $formValue;

    public function rules(): array
    {
        return [
            [['rid', 'createdAt', 'formKey', 'formValue'], 'required'],
            [['rid'], 'string', 'max' => 64],
            [['formKey'], 'string', 'max' => 255],
            [['createdAt'], 'safe'],
            ['formKey', 'validateFormKey'],
            ['formValue', 'string', 'max' => 255],
            [['rid'], 'required'],
            [['rid'], 'validateClientChat'],
        ];
    }
    /**
     * @param $attribute
     */
    public function validateClientChat($attribute): void
    {
        if (!ClientChat::findOne(['cch_rid' => $this->rid])) {
            $this->addError($attribute, 'ClientChat not found.');
        }
    }

    public function validateFormKey($attributes): void
    {
        $formKey = $this->formKey;
        if (!$formKey) {
            $this->addError('data', 'Undefined index: formKey in data request');
        }
        if (!ClientChatForm::findOne(['ccf_key' => $formKey])) {
            $this->addError('data', 'formKey not found.');
        }
    }

    /**
     * @param ClientChat $clientChat
     * @return bool
     */
    public function syncWithDb(ClientChat $clientChat): bool
    {
        /** @var ClientChatForm $clientChatForm */
        $clientChatForm = ClientChatForm::find()->where(['ccf_key' => $this->formKey])->one();

        if (is_null($clientChatForm)) {
            return false;
        }

        if (
            ClientChatFormResponse::find()
            ->where([
                'ccfr_form_id' => $clientChatForm->ccf_id,
                'ccfr_client_chat_id' => $clientChat->cch_id,
                'ccfr_value' => $this->formValue
            ])->exists()
        ) {
            return true;
        }

        $model = new ClientChatFormResponse();
        $model->load([
            'ccfr_uid' => $this->rid,
            'ccfr_client_chat_id' => $clientChat->cch_id,
            'ccfr_form_id' => $clientChatForm->ccf_id,
            'ccfr_value' => $this->formValue,
            'ccfr_rc_created_dt' => date('Y-m-d H:i:s', strtotime($this->createdAt)),
            'ccfr_created_dt' => date('Y-m-d H:i:s')
        ], '');

        return $model->save();
    }
}
