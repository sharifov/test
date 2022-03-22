<?php

namespace webapi\src\forms\payment\creditCard;

use yii\base\Model;

/**
 * Class CreditCardForm
 */
class CreditCardForm extends Model
{
    public const SCENARIO_WITHOUT_PRIVATE_DATA = 'without_private_data';

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
            [['expiration_month', 'expiration_year', 'holder_name'], 'required'],

            [['number', 'cvv'], 'required', 'on' => self::SCENARIO_DEFAULT],

            [['expiration_month'], 'integer', 'min' => 1, 'max' => 12],
            [['expiration_year'], 'integer', 'min' => 0],

            [['number'], 'string', 'max' => 20],
            [['number'], 'filter', 'filter' => static function ($value) {
                return str_replace(' ', '', $value);
            }],

            [['holder_name'], 'string', 'max' => 50],

            [['cvv'], 'string', 'max' => 4],
        ];
    }

    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios[self::SCENARIO_WITHOUT_PRIVATE_DATA] = $scenarios[self::SCENARIO_DEFAULT];
        return $scenarios;
    }

    public function formName(): string
    {
        return '';
    }
}
