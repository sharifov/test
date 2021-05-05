<?php

namespace modules\order\src\forms\api\createC2b;

use common\components\validators\PhoneValidator;
use yii\validators\EmailValidator;

/***
 * Class ContactInfoForm
 * @package modules\order\src\forms\api\createC2b
 *
 * @property string $first_name
 * @property string|null $last_name
 * @property string|null $middle_name
 * @property string|null $phone
 * @property string $email
 */
class ContactsInfoForm extends \yii\base\Model
{
    public string $first_name = '';
    public ?string $last_name = null;
    public ?string $middle_name = null;
    public ?string $phone = null;
    public string $email = '';

    public function rules()
    {
        return [
            [['first_name', 'email'], 'required'],
            [['first_name', 'last_name', 'middle_name'], 'string', 'max' => 50],

            ['phone', 'string', 'max' => 20],
            ['phone', PhoneValidator::class],

            ['email', 'string', 'max' => 100],
            ['email', 'email']
        ];
    }

    public function formName()
    {
        return 'contactsInfo';
    }
}
