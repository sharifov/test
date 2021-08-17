<?php

namespace webapi\src\forms\boWebhook;

/**
 * Class FlightRefundUpdateForm
 * @package webapi\src\boWebhook
 *
 * @property string $booking_id
 */
class FlightRefundUpdateForm extends \yii\base\Model
{
    public $booking_id;

    public function rules(): array
    {
        return [
            [['booking_id'], 'required'],
            [['booking_id'], 'string'],
        ];
    }

    public function formName(): string
    {
        return '';
    }
}
