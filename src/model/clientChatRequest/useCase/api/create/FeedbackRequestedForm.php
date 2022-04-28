<?php

namespace src\model\clientChatRequest\useCase\api\create;

use yii\base\Model;

/**
 * Class FeedbackRequestedForm
 * @package src\model\clientChatRequest\useCase\api\create
 */
class FeedbackRequestedForm extends Model
{
    public $id;
    public $rid;
    public $type;
    public $template;
    public $createdAt;
    public $triggerSource;
    public $requestedBy;

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            [['id', 'rid', 'type', 'template', 'createdAt', 'triggerSource', 'requestedBy'], 'required'],
            [['id', 'rid', 'type', 'template', 'createdAt', 'triggerSource'], 'string'],
            ['requestedBy', 'validateRequestedBy'],
            ['type', 'in', 'range' => ['sticky', 'fullscreen', 'questions', 'inline']],
            ['triggerSource', 'in', 'range' => ['agent', 'chat-close', 'bot']],
        ];
    }

    /**
     * @param $attribute
     * @param $params
     * @param $validator
     */
    public function validateRequestedBy($attribute, $params, $validator): void
    {
        if (!isset($this->$attribute['name'])) {
            $this->addError($attribute, "the `{$attribute}` field should contain `name` field");
        }
        if (!isset($this->$attribute['username'])) {
            $this->addError($attribute, "the `{$attribute}` field should contain `username` field");
        }
    }
}
