<?php

namespace modules\flight\src\useCases\voluntaryRefundInfo\form;

/**
 * Class VoluntaryRefundInfoForm
 * @package modules\flight\src\useCases\voluntaryRefundInfo
 *
 * @property string|null $booking_id
 */
class VoluntaryRefundInfoForm extends \yii\base\Model
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
