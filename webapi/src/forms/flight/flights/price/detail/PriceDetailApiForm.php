<?php

namespace webapi\src\forms\flight\flights\price\detail;

use modules\flight\models\FlightPax;
use yii\base\Model;

/**
 * Class PriceDetailApiForm
 *
 * @property $paxType
 * @property $selling
 * @property $fare
 * @property $baseTaxes
 * @property $taxes
 * @property $tickets
 * @property $insurance
 */
class PriceDetailApiForm extends Model
{
    public $paxType;
    public $selling;
    public $fare;
    public $baseTaxes;
    public $taxes;
    public $tickets;
    public $insurance;

    public function __construct(string $paxType, $config = [])
    {
        $this->paxType = $paxType;
        parent::__construct($config);
    }

    public function rules(): array
    {
        return [
            [['paxType'], 'required'],
            [['paxType'], 'string', 'max' => 3],
            [['FlightPax'], 'in', 'range' => array_keys(FlightPax::PAX_LIST_ID)],

            [['tickets'], 'integer'],

            [['selling'], 'number'],
            [['insurance'], 'number'],
            [['fare'], 'number'],
            [['taxes'], 'number'],
            [['baseTaxes'], 'number'],
        ];
    }
}
