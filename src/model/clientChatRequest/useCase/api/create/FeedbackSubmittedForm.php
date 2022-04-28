<?php

namespace src\model\clientChatRequest\useCase\api\create;

use yii\base\DynamicModel;
use yii\base\Model;

/**
 * Class FeedbackSubmittedForm
 * @package src\model\clientChatRequest\useCase\api\create
 */
class FeedbackSubmittedForm extends Model
{
    public $id;
    public $rid;
    public $type;
    public $template;
    public $createdAt;
    public $triggerSource;
    public $requestedBy;
    public $responses;

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            [['id', 'rid', 'type', 'template', 'createdAt', 'triggerSource', 'requestedBy'], 'required'],
            [['id', 'rid', 'type', 'template', 'createdAt', 'triggerSource'], 'string'],
            ['requestedBy', 'validateRequestedBy'],
            ['responses', 'validateResponses'],
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

    /**
     * @param $attribute
     * @param $params
     * @param $validator
     */
    public function validateResponses($attribute, $params, $validator): void
    {
        /** @var array $rules The rules list for response item */
        $rules = [
            [['question'], 'string'],
            [['response'], function ($responseAttribute, $responseParams, $responseValidator) {
                if (!in_array(gettype($this->$responseAttribute), ['string', 'boolean', 'integer'])) {
                    $this->addError($responseAttribute, "unexpected type, available types: string, boolean, integer");
                }
            }]
        ];
        /** @var array $errors Reducing the error list */
        $errors = array_reduce($this->$attribute, function ($result, $response) use ($rules) {
            $fields = ['question' => $response['question'], 'response' => $response['response']];
            $model = DynamicModel::validateData($fields, $rules);
            if ($model->hasErrors()) {
                $result[] = $model->errors;
            }
            return $result;
        }, []);

        // If errors is not empty - add errors into parent model
        if (count($errors) > 0) {
            $this->addError($attribute, $errors);
        }
    }
}
