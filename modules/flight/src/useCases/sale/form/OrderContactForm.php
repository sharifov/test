<?php

namespace modules\flight\src\useCases\sale\form;

use common\components\validators\PhoneValidator;
use yii\base\Model;
use yii\helpers\ArrayHelper;

/**
 * Class OrderContactForm
 *
 * @property $email
 * @property $first_name
 * @property $last_name
 * @property $phone_number
 */
class OrderContactForm extends Model
{
    public $email;
    public $first_name;
    public $last_name;
    public $phone_number;

    public function rules(): array
    {
        return [
            ['email', 'required', 'when' => function () {
                return empty($this->phone_number);
            }],
            ['email', 'string', 'max' => 100],
            ['email', 'email', 'skipOnEmpty' => true, 'skipOnError' => true],

            ['phone_number', 'required', 'when' => function () {
                return empty($this->email);
            }],
            ['phone_number', 'string', 'max' => 20],
            ['phone_number', 'filter', 'filter' => static function ($value) {
                return $value === null ? null : str_replace(['-', ' '], '', trim($value));
            }],
            ['phone_number', PhoneValidator::class, 'skipOnEmpty' => true, 'skipOnError' => true],

            [['first_name', 'last_name'], 'string', 'max' => 50],
            [['first_name', 'last_name'], 'filter', 'filter' => static function ($value) {
                return self::cleanName($value);
            }],
            ['first_name', 'default', 'value' => 'ClientName'],
        ];
    }

    public function formName(): string
    {
        return '';
    }

    public static function fillForm(array $saleData): OrderContactForm
    {
        $form = new self();
        $form->email = ArrayHelper::getValue($saleData, 'email');
        $form->phone_number = ArrayHelper::getValue($saleData, 'phone');
        $form->first_name = ArrayHelper::getValue($saleData, 'customerInfo.firstName');
        $form->last_name = ArrayHelper::getValue($saleData, 'customerInfo.lastName');
        return $form;
    }

    public static function cleanName(?string $name): ?string
    {
        if ($name) {
            $name = preg_replace('/[^a-z-\s]/ui', '', $name);
            return preg_replace('|[\s]+|s', ' ', $name);
        }
        return $name;
    }
}
