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
            ['email', 'string', 'max' => 100],
            ['email', 'email', 'skipOnEmpty' => true, 'skipOnError' => true],

            ['first_name', 'string', 'max' => 50],
            ['last_name', 'string', 'max' => 50],

            ['phone_number', 'string', 'max' => 20],
            ['phone_number', PhoneValidator::class, 'skipOnEmpty' => true, 'skipOnError' => true],
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
}
