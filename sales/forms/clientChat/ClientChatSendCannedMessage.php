<?php

namespace sales\forms\clientChat;

use yii\base\Model;
use yii\helpers\HtmlPurifier;

class ClientChatSendCannedMessage extends Model
{
    public $message;

    public $chatId;

    public function rules(): array
    {
        return [
            ['message', 'string'],
            ['chatId', 'integer'],
            [['message', 'chatId'], 'required'],
            ['message', 'filter', 'filter' => static function ($value) {
                return HtmlPurifier::process($value);
            }],
            ['chatId', 'filter', 'filter' => 'intval']
        ];
    }
}
