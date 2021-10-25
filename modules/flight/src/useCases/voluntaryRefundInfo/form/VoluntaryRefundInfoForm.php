<?php

namespace modules\flight\src\useCases\voluntaryRefundInfo\form;

/**
 * Class VoluntaryRefundInfoForm
 * @package modules\flight\src\useCases\voluntaryRefundInfo
 *
 * @property string|null $bookingId
 */
class VoluntaryRefundInfoForm extends \yii\base\Model
{
    public $bookingId;

    public function rules(): array
    {
        return [
            [['bookingId'], 'required'],
            [['bookingId'], 'string', 'max' => 10],
        ];
    }

    public function formName(): string
    {
        return '';
    }
}
