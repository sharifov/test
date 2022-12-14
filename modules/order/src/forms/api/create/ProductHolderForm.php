<?php

namespace modules\order\src\forms\api\create;

use common\components\validators\PhoneValidator;

/**
 * Class ProductHolderForm
 * @package modules\order\src\forms\api\create
 *
 * @property string $firstName
 * @property string $lastName
 * @property string|null $middleName
 * @property string $email
 * @property string $phone
 */
class ProductHolderForm extends \yii\base\Model
{
    public $firstName;

    public $lastName;

    public $middleName;

    public $email;

    public $phone;

    public function rules()
    {
        return [
            [['firstName', 'lastName', 'email', 'phone'], 'required'],

            [['firstName', 'lastName', 'middleName'], 'string', 'max' => 50],

            [['email'], 'string', 'max' => 100],
            [['email'], 'email'],

            [['phone'], 'string', 'max' => 20],
            [['phone'], PhoneValidator::class],
        ];
    }

    public function formName(): string
    {
        return 'productHolder';
    }
}
