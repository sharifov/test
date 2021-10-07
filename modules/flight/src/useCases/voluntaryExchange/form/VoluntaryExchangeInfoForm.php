<?php

namespace modules\flight\src\useCases\voluntaryExchange\form;

use yii\base\Model;

/**
 * Class VoluntaryExchangeInfoForm
 *
 * @property $booking_id
 */
class VoluntaryExchangeInfoForm extends Model
{
    public $booking_id;

    public function rules(): array
    {
        return [
            [['booking_id'], 'required'],
            [['booking_id'], 'string', 'max' => 10],
        ];
    }

    public function formName(): string
    {
        return '';
    }
}
