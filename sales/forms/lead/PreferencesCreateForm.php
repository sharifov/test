<?php

namespace sales\forms\lead;

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

    public function rules(): array
    {
        return [

            [['marketPrice', 'clientsBudget'], 'number', 'min' => 500, 'max' => 99000],

            ['numberStops', 'integer','min' => 0, 'max' => 7],

        ];
    }

    public static function listNumberStops(): array
    {
        return [
            0 => 0,
            1 => 1,
            2 => 2,
            3 => 3,
            4 => 4,
            5 => 5,
            6 => 6,
            7 => 7,
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'marketPrice' => 'Market, $',
            'clientsBudget' => 'Budget, $',
            'numberStops' => 'Stops',
        ];
    }

}
