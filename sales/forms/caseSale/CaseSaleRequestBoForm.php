<?php

namespace sales\forms\caseSale;

use borales\extensions\phoneInput\PhoneInputValidator;
use yii\base\Model;

/**
 * Class CaseSaleRequestBoForm
 *
 * @property string|null $email
 * @property string|null $phone
 * @property string|null $orderUid
 */
class CaseSaleRequestBoForm extends Model
{
    public $email;
    public $phone;
    public $orderUid;

    public function rules(): array
    {
        return [
            ['email', 'string', 'max' => 100],
            ['email', 'email', 'skipOnEmpty' => true],

            ['phone', 'string', 'max' => 20],
            ['phone', PhoneInputValidator::class],

            ['orderUid', 'string', 'max' => 20],

            ['orderUid', 'checkEmptyParams']
        ];
    }

    /**
     * @param $attribute
     */
    public function checkEmptyParams($attribute): void
    {
        if (empty($this->orderUid) && empty($this->phone) && empty($this->email)) {
            $this->addError($attribute, 'At least one parameter(orderUid,phone,email) must be filled');
        }
    }
}
