<?php

namespace webapi\src\forms\payment\creditCard;

use yii\base\Model;

/**
 * Class CreditCardForm
 */
class CreditCardForm extends Model
{
    public $number;
    public $holder_name;
    public $expiration_month;
    public $expiration_year;
    public $cvv;

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['number', 'expiration_month', 'expiration_year', 'cvv'], 'required'],

            [['expiration_month', 'expiration_year'], 'integer'],

            [['number'], 'string', 'max' => 20],
            [['number'], 'filter', 'filter' => static function ($value) {
                return str_replace(' ', '', $value);
            }],

            [['holder_name'], 'string', 'max' => 50],

            [['cvv'], 'string', 'max' => 4],
        ];
    }

    public function formName(): string
    {
        return '';
    }
}