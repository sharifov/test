<?php

namespace webapi\src\forms\flight\flights\price\detail;

use common\models\Currency;
use modules\flight\models\FlightPax;
use yii\base\Model;

/**
 * Class PriceDetailApiForm
 *
 * @property $paxType
 * @property $currency
 * @property $selling
 * @property $fare
 * @property $baseTaxes
 * @property $taxes
 * @property $tickets
 * @property $insurance
 */
class PriceDetailApiForm extends Model
{
    public $selling;
    public $fare;
    public $baseTaxes;
    public $taxes;
    public $tickets;
    public $insurance;
    public $currency;
    public $paxType;

    public function __construct(string $paxType, string $currency, $config = [])
    {
        $this->paxType = $paxType;
        $this->currency = $currency;
        parent::__construct($config);
    }

    public function rules(): array
    {
        return [
            [['paxType'], 'required'],
            [['paxType'], 'string', 'max' => 3],
            [['paxType'], 'in', 'range' => array_keys(FlightPax::PAX_LIST_ID)],

            [['currency'], 'required'],
            [['currency'], 'string', 'max' => 3],
            [['currency'], 'exist', 'skipOnError' => true, 'targetClass' => Currency::class, 'targetAttribute' => ['currency' => 'cur_code']],

            [['tickets'], 'integer'],

            [['selling'], 'number'],
            [['insurance'], 'number'],
            [['fare'], 'number'],
            [['taxes'], 'number'],
            [['baseTaxes'], 'number'],
        ];
    }

    public function formName(): string
    {
        return '';
    }
}
