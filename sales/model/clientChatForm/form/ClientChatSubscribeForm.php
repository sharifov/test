<?php

namespace sales\model\clientChatForm\form;

/**
 * Class ClientChatSubscribeForm
 * @package sales\model\clientChatForm\form
 *
 * @property-read null|string $chat_visitor_id
 * @property-read null|string $subscription_uid
 * @property-read null|string $chat_room_id
 * @property-read null|string $expired_date
 */
class ClientChatSubscribeForm extends \yii\base\Model
{
    public ?string $chat_visitor_id = null;

    public ?string $subscription_uid = null;

    public ?string $chat_room_id = null;

    public ?string $expired_date = null;

    public function rules(): array
    {
        return [
            [['subscription_uid'], 'required'],
            [['subscription_uid'], 'string', 'max' => 100],

            [['chat_visitor_id', 'chat_room_id', 'expired_date'], 'string'],
            [['expired_date'], 'date', 'format' => 'php:Y-m-d'],
        ];
    }

    public function formName(): string
    {
        return '';
    }
}
