<?php

namespace sales\forms\lead;

use sales\helpers\lead\LeadPreferencesHelper;
use yii\base\Model;

/**
 * Class PreferencesCreateForm
 * @property integer $clientsBudget
 * @property integer $marketPrice
 * @property integer $numberStops
 */
class PreferencesCreateForm extends Model
{

    public $marketPrice;
    public $clientsBudget;
    public $numberStops;

    /**
     * @return array
     */
    public function rules(): array
    {
        return [

            [['marketPrice', 'clientsBudget'], 'number', 'min' => 500, 'max' => 99000],

            ['numberStops', 'integer'],
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
        ];
    }

}
