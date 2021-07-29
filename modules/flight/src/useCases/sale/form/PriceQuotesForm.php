<?php

namespace modules\flight\src\useCases\sale\form;

use modules\flight\models\FlightPax;
use yii\base\Model;

/**
 * Class PriceQuotesForm
 *
 * @property $paxType
 * @property $selling
 * @property $net
 * @property $fare
 * @property $taxes
 * @property $mark_up
 * @property $cnt
 *
 * @property $paxTypeId
 */
class PriceQuotesForm extends Model
{
    public $paxType;
    public $selling;
    public $net;
    public $fare;
    public $taxes;
    public $mark_up;
    public $cnt;

    private $paxTypeId;

    public function rules(): array
    {
        return [
            [['paxType'], 'required'],
            [['paxType'], 'string', 'max' => 3],
            [['paxType'], 'in', 'range' => array_keys(FlightPax::PAX_LIST_ID)],

            [['selling'], 'number'],
            [['net'], 'number'],
            [['fare'], 'number'],
            [['taxes'], 'number'],
            [['mark_up'], 'number'],

            [['cnt'], 'default', 'value' => 1],
            [['cnt'], 'integer'],
        ];
    }

    public function formName(): string
    {
        return '';
    }

    public function getPaxTypeId(): ?int
    {
        return FlightPax::getPaxId($this->paxType);
    }
}
