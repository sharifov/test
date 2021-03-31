<?php

namespace modules\order\src\forms\api\createC2b;

use common\components\validators\PhoneValidator;

/**
 * Class ProductHolderForm
 * @package modules\order\src\forms\api\create
 *
 * @property string $firstName
 * @property string $lastName
 * @property string $email
 * @property string $phone
 */
class ProductHolderForm extends \yii\base\Model
{
    public $firstName;

    public $lastName;

    public $email;

    public $phone;

    public function rules()
    {
        return [
            [['firstName', 'lastName', 'email', 'phone'], 'required'],

            [['firstName', 'lastName'], 'string', 'max' => 50],

            [['email'], 'string', 'max' => 100],
            [['email'], 'email'],

            [['phone'], 'string', 'max' => 20],
            [['phone'], PhoneValidator::class],
        ];
    }

    public function formName(): string
    {
        return 'holder';
    }
}
