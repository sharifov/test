<?php

namespace modules\flight\src\useCases\voluntaryExchangeCreate\form\flightQuote\tripsForm\segmentsForm\stops;

use sales\traits\FormNameModelTrait;
use yii\base\Model;

/**
 * Class StopForm
 *
 * @property $locationCode
 * @property $departureDateTime
 * @property $arrivalDateTime
 * @property $duration
 * @property $elapsedTime
 * @property $equipment
 */
class StopForm extends Model
{
    use FormNameModelTrait;

    public $locationCode;
    public $departureDateTime;
    public $arrivalDateTime;
    public $duration;
    public $elapsedTime;
    public $equipment;

    public function rules(): array
    {
        return [
            [['departureDateTime', 'arrivalDateTime'], 'datetime', 'format' => 'php:Y-m-d H:i'],

            [['locationCode'], 'string', 'max' => 3],

            [['duration', 'elapsedTime', 'equipment'], 'integer'],
        ];
    }
}
