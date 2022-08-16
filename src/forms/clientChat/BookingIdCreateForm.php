<?php

namespace src\forms\clientChat;

use yii\base\Model;

/**
 * Class BookingIdCreateForm
 *
 * @property string $bookingId
 */
class BookingIdCreateForm extends Model
{
    public $bookingId;

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            ['bookingId', 'required'],
            ['bookingId', 'string', 'min' => 7, 'max' => 20],
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels(): array
    {
        return [
            'bookingId' => 'Booking id',
        ];
    }
}
