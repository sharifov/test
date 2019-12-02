<?php

namespace sales\events\sms;

use common\models\Sms;

/**
 * Class SmsCreatedEvent
 *
 * @property Sms $sms
 */
class SmsCreatedEvent
{
    public $sms;

    /**
     * @param Sms $sms
     */
    public function __construct(Sms $sms)
    {
        $this->sms = $sms;
    }
}
