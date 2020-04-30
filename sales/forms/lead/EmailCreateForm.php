<?php

namespace sales\forms\lead;

use common\models\Client;
use common\models\ClientEmail;
use yii\base\Model;

/**
 * Class EmailCreateForm
 * @property string $email
 * @property string $help - only for View for multiInput Widget
 * @property boolean $required
 * @property string $message
 * @property string $ce_title
 * @property $type
 */
class EmailCreateForm extends Model
{
	/**
	 * @var string
	 */
	public $email;

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
    public $message = 'Email cannot be blank.';
    public $ce_title;

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            ['email', 'validateRequired', 'skipOnEmpty' => false],
			['email', 'default', 'value' => null],
            [['email', 'ce_title'], 'string', 'max' => 100],
            ['email', 'email'],
			[['type', 'client_id', 'id'], 'integer'],
			['email', 'filter', 'filter' => static function($value) {
                return $value === null ? null : mb_strtolower(trim($value));
            }],
			[['client_id'], 'exist', 'skipOnError' => true, 'targetClass' => Client::class, 'targetAttribute' => ['client_id' => 'id']],
			[['email', 'client_id'], 'unique', 'targetClass' => ClientEmail::class, 'targetAttribute' => ['email', 'client_id'], 'except' => 'update', 'message' => 'Client already has this email',],
			['email', 'checkUniqueClientEmail', 'on' => 'update'],
			['type', 'checkTypeForExistence'],
		];
    }

    public function validateRequired($attribute, $params): void
    {
        if ($this->required && !$this->email) {
            $this->addError($attribute, $this->message);
        }
    }

	/**
	 * @param $attribute
	 * @param $params
	 */
	public function checkTypeForExistence($attribute, $params): void
	{
		if (!isset(ClientEmail::EMAIL_TYPE[$this->type])) {
			$this->addError($attribute, 'Type of the email is not found');
		}
	}

	/**
	 * @param $attribute
	 * @param $params
	 */
	public function checkUniqueClientEmail($attribute, $params): void
	{
		$email = ClientEmail::find()->where('id<>:id', [':id' => $this->id])->andWhere(['email' => $this->email, 'client_id' => $this->client_id])->exists();
		if ($email) {
			$this->addError($attribute, 'Client already has this email');
		}
	}

}
