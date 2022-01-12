<?php

namespace src\model\clientChatForm\form;

/**
 * Class ClientChatUnsubscribeForm
 * @package src\model\clientChatForm\form
 *
 * @property-read null|string $subscription_uid
 */
class ClientChatUnsubscribeForm extends \yii\base\Model
{
    public ?string $subscription_uid = null;

    public function rules(): array
    {
        return [
            [['subscription_uid'], 'required'],
            [['subscription_uid'], 'string', 'max' => 100],
        ];
    }

    public function formName(): string
    {
        return '';
    }
}
