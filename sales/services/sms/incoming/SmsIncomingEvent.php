<?php

namespace sales\services\sms\incoming;

use common\models\Sms;

/**
 * Class SmsIncomingEvent
 *
 * @property Sms $sms
 */
class SmsIncomingEvent
{
    public $sms;

    public function __construct(Sms $sms)
    {
        $this->sms = $sms;
    }
}
