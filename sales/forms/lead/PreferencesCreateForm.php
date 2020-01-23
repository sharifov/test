<?php

namespace sales\forms\lead;

use common\models\Currency;
use sales\helpers\lead\LeadPreferencesHelper;
use yii\base\Model;

/**
 * Class PreferencesCreateForm
 * @property integer $clientsBudget
 * @property integer $marketPrice
 * @property integer $numberStops
 * @property string $currency
 */
class PreferencesCreateForm extends Model
{

    public $marketPrice;
    public $clientsBudget;
    public $numberStops;
    public $currency;

    /**
     * @return array
     */
    public function rules(): array
    {
        return [

            [['marketPrice', 'clientsBudget'], 'number', 'min' => 500, 'max' => 99000],

            ['numberStops', 'integer'],
            [['currency'], 'string', 'max' => 3],
            [['currency'], 'default', 'value' => null],
            [['currency'], 'exist', 'skipOnError' => true, 'targetClass' => Currency::class, 'targetAttribute' => ['currency' => 'cur_code']],
            ['numberStops', 'in', 'range' => array_keys(LeadPreferencesHelper::listNumberStops())],

        ];
    }

    /**
     * @return array
     */
    public function attributeLabels(): array
    {
        return [
            'marketPrice' => 'Market, $',
            'clientsBudget' => 'Budget, $',
            'numberStops' => 'Stops',
            'currency' => 'Currency'
        ];
    }

}
