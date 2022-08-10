<?php

namespace src\model\clientChatRequest\useCase\api\create;

use DateTime;
use Exception;
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
            ['createdAt', 'date','format' => 'php:Y-m-d'],
            ['formValue', 'string', 'max' => 255],
        ];
    }

    public function validateCreatedAt($attribute)
    {
        try {
            new DateTime($this->createdAt);
        } catch (Exception $e) {
            $this->addError($attribute, "The format of " . $attribute . " is invalid.");
        }
    }
}
