<?php

namespace sales\forms\lead;

use borales\extensions\phoneInput\PhoneInputValidator;
use yii\base\Model;

/**
 * Class PhoneCreateForm
 * @property string $phone
 * @property string $help - only for View for multiInput Widget
 * @property boolean $phoneIsRequired
 * @property string $message
 */
class PhoneCreateForm extends Model
{

    public $phone;
    public $help;

    public $phoneIsRequired = true;
    public $message = 'Phone cannot be blank.';

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            ['phone', 'validatePhoneRequired', 'skipOnEmpty' => false],
            ['phone', 'string', 'max' => 100],
            ['phone', PhoneInputValidator::class],
            ['phone', 'filter', 'filter' => function($value) {
                return str_replace(['-', ' '], '', trim($value));
            }]
        ];
    }

    public function validatePhoneRequired($attribute, $params): void
    {
        if ($this->phoneIsRequired && !$this->phone) {
            $this->addError($attribute, $this->message);
        }
    }

}
