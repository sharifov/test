<?php

namespace frontend\widgets\clientChat;

use yii\base\Widget;

class ClientChatFormBookingIdWidget extends Widget
{
    public string $bookingId;

    public function run(): string
    {
        return $this->render('ccf_booking_id_item', ['bookingId' => $this->bookingId]);
    }
}
