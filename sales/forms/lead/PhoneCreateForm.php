<?php

namespace sales\forms\lead;

use borales\extensions\phoneInput\PhoneInputValidator;
use common\models\DepartmentPhoneProject;
use common\models\UserProjectParams;
use yii\base\Model;

/**
 * Class PhoneCreateForm
 * @property string $phone
 * @property string $help - only for View for multiInput Widget
 * @property boolean $required
 * @property string $message
 */
class PhoneCreateForm extends Model
{

    public $phone;
    public $help;

    public $required = false;
    public $message = 'Phone cannot be blank.';

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            ['phone', 'validateRequired', 'skipOnEmpty' => false],
            ['phone', 'string', 'max' => 100],
            ['phone', PhoneInputValidator::class],
            ['phone', 'filter', 'filter' => static function($value) {
                return str_replace(['-', ' '], '', trim($value));
            }],
			['phone', 'checkForExistence']
        ];
    }

    public function validateRequired($attribute, $params): void
    {
        if ($this->required && !$this->phone) {
            $this->addError($attribute, $this->message);
        }
    }

	/**
	 * @param $attribute
	 * @param $params
	 */
	public function checkForExistence($attribute, $params): void
	{
		if (DepartmentPhoneProject::find()->where(['dpp_phone_number' => $this->phone])->exists()) {
			$this->addError($attribute, 'This phone number is not allowed (General)');
		} elseif (UserProjectParams::find()->where(['upp_tw_phone_number' => $this->phone])->exists()) {
			$this->addError($attribute, 'This phone number is not allowed (Direct)');
		}
	}

}
