<?php

namespace sales\forms\lead;

use yii\base\Model;

/**
 * Class EmailCreateForm
 * @property string $email
 * @property string $help - only for View for multiInput Widget
 * @property boolean $emailIsRequired
 * @property string $message
 */
class EmailCreateForm extends Model
{

    public $email;
    public $help;

    public $emailIsRequired = false;
    public $message = 'Email cannot be blank.';

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            ['email', 'validateEmailRequired', 'skipOnEmpty' => false],
            ['email', 'string', 'max' => 100],
            ['email', 'email'],
            ['email', 'filter', 'filter' => function($value) {
                return mb_strtolower(trim($value));
            }]
        ];
    }

    public function validateEmailRequired($attribute, $params): void
    {
        if ($this->emailIsRequired && !$this->email) {
            $this->addError($attribute, $this->message);
        }
    }

}
