<?php

namespace sales\forms\clientChat;

use yii\base\Model;
use yii\helpers\HtmlPurifier;

class ClientChatSearchCannedResponse extends Model
{
    public string $query = '';

    public int $chatId = 0;

    public function rules(): array
    {
        return [
            ['query', 'string', 'min' => 3],
            ['chatId', 'integer'],
            [['query', 'chatId'], 'required'],
            ['query', 'filter', 'filter' => static function ($value) {
                $value = preg_replace('/[^\w\s]/ui', ' ', $value);
                $value = preg_replace('|[\s]+|s', ' ', trim($value));
                return str_replace(' ', ' & ', $value);
            }],
            ['chatId', 'filter', 'filter' => 'intval']
        ];
    }
}
