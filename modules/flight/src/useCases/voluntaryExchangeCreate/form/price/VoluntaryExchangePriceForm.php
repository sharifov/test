<?php

namespace modules\flight\src\useCases\voluntaryExchangeCreate\form\price;

use sales\traits\FormNameModelTrait;
use yii\base\Model;

/**
 * Class VoluntaryExchangePriceForm
 */
class VoluntaryExchangePriceForm extends Model
{
    use FormNameModelTrait;

    public $totalPrice;
    public $comm;
    public $isCk;

    public function rules(): array
    {
        return [
            [['totalPrice'], 'required'],
            [['totalPrice'], 'number'],

            [['comm'], 'number'],

            [['isCk'], 'boolean'],
        ];
    }
}
