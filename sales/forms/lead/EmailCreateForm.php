<?php

namespace sales\forms\lead;

use yii\base\Model;

/**
 * Class EmailCreateForm
 * @property string $email
 * @property string $help - only for View for multiInput Widget
 * @property boolean $required
 * @property string $message
 */
class EmailCreateForm extends Model
{

    public $email;
    public $help;

    public $required = false;
    public $message = 'Email cannot be blank.';

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            ['email', 'validateRequired', 'skipOnEmpty' => false],
            ['email', 'string', 'max' => 100],
            ['email', 'email'],
            ['email', 'filter', 'filter' => static function($value) {
                return mb_strtolower(trim($value));
            }]
        ];
    }

    public function validateRequired($attribute, $params): void
    {
        if ($this->required && !$this->email) {
            $this->addError($attribute, $this->message);
        }
    }

}
