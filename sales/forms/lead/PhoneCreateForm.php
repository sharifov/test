<?php

namespace sales\forms\lead;

use borales\extensions\phoneInput\PhoneInputValidator;
use common\models\ClientPhone;
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

	/**
	 * @var string
	 */
    public $phone;

	/**
	 * @var integer
	 */
	public $id;

	/**
	 * @var integer
	 */
	public $client_id;

	/**
	 * @var integer
	 */
    public $type;

    public $help;

    public $required = false;

    public $message = 'Phone cannot be blank.';


	/**
     * @return array
     */
    public function rules(): array
    {
        return [
            ['phone', 'required'],
            ['phone', 'validateRequired', 'skipOnEmpty' => false],
            ['phone', 'string', 'max' => 100],
            ['phone', PhoneInputValidator::class],
            ['phone', 'filter', 'filter' => static function($value) {
                return str_replace(['-', ' '], '', trim($value));
            }],
			['phone', 'checkForExistence'],
			[['type', 'client_id', 'id'], 'integer'],
			['type', 'checkTypeForExistence'],
			[['phone', 'client_id'], 'unique', 'targetClass' => ClientPhone::class,  'targetAttribute' => ['phone', 'client_id'], 'message' => 'Client already has this phone number', 'except' => 'update'],
			['phone', 'checkUniqueClientPhone', 'on' => 'update']
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

	/**
	 * @param $attribute
	 * @param $params
	 */
	public function checkTypeForExistence($attribute, $params): void
	{
		if (!isset(ClientPhone::PHONE_TYPE[$this->type])) {
			$this->addError($attribute, 'Type of the phone is not found');
		}
	}

	/**
	 * @param $attribute
	 * @param $params
	 */
	public function checkUniqueClientPhone($attribute, $params): void
	{
		$phone = ClientPhone::find()->where('id<>:id', [':id' => $this->id])->andWhere(['phone' => $this->phone, 'client_id' => $this->client_id])->exists();
		if ($phone) {
			$this->addError($attribute, 'Client already has this phone number');
		}
	}
}
