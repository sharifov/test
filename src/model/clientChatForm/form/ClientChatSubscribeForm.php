<?php

namespace src\model\clientChatForm\form;

/**
 * Class ClientChatSubscribeForm
 * @package src\model\clientChatForm\form
 *
 * @property-read null|string $subscription_uid
 * @property-read null|string $expired_date
 */
class ClientChatSubscribeForm extends \yii\base\Model
{
    public ?string $subscription_uid = null;

    public ?string $expired_date = null;

    public function rules(): array
    {
        return [
            [['subscription_uid'], 'required'],
            [['subscription_uid'], 'string', 'max' => 100],

            [['expired_date'], 'date', 'format' => 'php:Y-m-d'],
        ];
    }

    public function formName(): string
    {
        return '';
    }
}
