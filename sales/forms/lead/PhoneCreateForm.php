<?php

namespace sales\forms\lead;

use borales\extensions\phoneInput\PhoneInputValidator;
use common\models\ClientPhone;
use sales\services\client\InternalPhoneValidator;
use yii\base\Model;

/**
 * Class PhoneCreateForm
 * @property string $phone
 * @property string $help - only for View for multiInput Widget
 * @property boolean $required
 * @property string $message
 * @property string $comments
 * @property string $cp_title
 * @property $type
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

    public $comments;
    public $cp_title;


    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            ['phone', 'validateRequired', 'skipOnEmpty' => false],
            ['phone', 'default', 'value' => null],
            [['phone', 'cp_title'], 'string', 'max' => 100],
            ['phone', PhoneInputValidator::class],
            ['phone', 'filter', 'filter' => static function ($value) {
                return $value === null ? null : str_replace(['-', ' '], '', trim($value));
            }],
            ['phone', InternalPhoneValidator::class, 'allowInternalPhone' => \Yii::$app->params['settings']['allow_contact_internal_phone']],
            [['type', 'client_id', 'id'], 'integer'],
            ['type', 'checkTypeForExistence'],
            [['phone', 'client_id'], 'unique', 'targetClass' => ClientPhone::class,  'targetAttribute' => ['phone', 'client_id'], 'message' => 'Client already has this phone number', 'except' => 'update'],
            ['phone', 'checkUniqueClientPhone', 'on' => 'update'],
            ['comments', 'string'],
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
    public function checkTypeForExistence($attribute, $params): void
    {
        if (!ClientPhone::getPhoneType($this->type)) {
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
