<?php

namespace sales\forms\caseSale;

use yii\base\Model;

/**
 * Class CaseSaleSendCcInfoForm
 * @package sales\forms\caseSale
 *
 * @property string $email
 * @property array $emailList
 */
class CaseSaleSendCcInfoForm extends Model
{
    public string $email = '';

    public array $emailList = [];

    public function rules(): array
    {
        return [
            ['email', 'string'],
            ['email', 'required'],
            ['email', 'checkIfEmailInList']
        ];
    }

    public function checkIfEmailInList($attribute): void
    {
        if (!in_array($this->$attribute, $this->emailList, false)) {
            $this->addError('email', 'Invalid email address');
        }
    }
}
