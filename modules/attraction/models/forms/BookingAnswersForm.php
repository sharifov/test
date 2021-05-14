<?php

namespace modules\attraction\models\forms;

use yii\base\Model;

class BookingAnswersForm extends Model
{
    public string $bookingId = '';
    public string $quoteId = '';
    public string $leadPassengerName = '';
    public array $booking_answers = [];

    public function rules()
    {
        return [
            ['bookingId', 'required'],
            ['quoteId', 'required'],
            ['leadPassengerName', 'required'],
            [['booking_answers'], 'required']
        ];
    }
}
