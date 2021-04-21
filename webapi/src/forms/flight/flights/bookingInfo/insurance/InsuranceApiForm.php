<?php

namespace  webapi\src\forms\flight\flights\bookingInfo\insurance;

use common\models\Currency;
use yii\base\Model;

/**
 * Class InsuranceApiForm
 *
 * @property $paid
 * @property $currency
 * @property $amount
 * @property $policyNumber
 */
class InsuranceApiForm extends Model
{
    public $paid;
    public $currency;
    public $amount;
    public $policyNumber;

    public function rules(): array
    {
        return [
            [['paid', 'currency', 'amount'], 'required'],

            [['paid'], 'boolean'],

            [['currency'], 'string', 'max' => 3],
            [['currency'], 'exist', 'skipOnError' => true, 'targetClass' => Currency::class, 'targetAttribute' => ['currency' => 'cur_code']],

            [['amount'], 'number'],

            [['policyNumber'], 'integer'],
        ];
    }

    public function formName(): string
    {
        return '';
    }
}
