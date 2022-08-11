<?php

namespace src\model\clientChatRequest\useCase\api\create;

use src\model\clientChat\useCase\create\ClientChatRepository;
use yii\base\Model;

/**
 * Interface FeedbackFormInterface
 * @package src\model\clientChatRequest\useCase\api\create
 */
abstract class FeedbackFormBase extends Model implements FeedbackFormInterface
{
    const EVENT_FEEDBACK_REQUESTED = 'FEEDBACK_REQUESTED';
    const EVENT_FEEDBACK_REJECTED = 'FEEDBACK_REJECTED';
    const EVENT_FEEDBACK_SUBMITTED = 'FEEDBACK_SUBMITTED';

    public $id;
    public $rid;
    public $type;
    public $template;
    public $createdAt;
    public $triggerSource;
    public $requestedBy;
    public $requestedFor;

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            [['id', 'rid', 'type', 'createdAt', 'triggerSource', 'requestedFor'], 'required'],
            [['id', 'rid', 'type', 'createdAt', 'triggerSource', 'template'], 'string'],
            ['rid', 'validateRoomId'],
            ['requestedBy', 'validateRequestedBy'],
            ['requestedFor', 'validateRequestedFor'],
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
        if (!is_null($this->$attribute)) {
            if (!isset($this->$attribute['name'])) {
                $this->addError($attribute, "the `{$attribute}` field should contain `name` field");
            }
            if (!isset($this->$attribute['username'])) {
                $this->addError($attribute, "the `{$attribute}` field should contain `username` field");
            }
        }
    }

    /**
     * @param $attribute
     * @param $params
     * @param $validator
     */
    public function validateRequestedFor($attribute, $params, $validator): void
    {
        if (!isset($this->$attribute['name'])) {
            $this->addError($attribute, "the `{$attribute}` field should contain `name` field");
        }
        if (!isset($this->$attribute['username'])) {
            $this->addError($attribute, "the `{$attribute}` field should contain `username` field");
        }
    }

    /**
     * @param $attribute
     * @param $params
     * @param $validator
     * @throws \yii\base\InvalidConfigException
     */
    public function validateRoomId($attribute, $params, $validator): void
    {
        $ownerUsername = (isset($this->requestedFor['username'])) ? $this->requestedFor['username'] : null;
        $repo = \Yii::createObject(ClientChatRepository::class);
        if (is_null($repo->getByRidAndOwnerUsername($this->rid, $ownerUsername))) {
            $this->addError($attribute, "the chat with room id `{$this->rid}` is not exist, or exist but own by another user");
        }
    }
}
