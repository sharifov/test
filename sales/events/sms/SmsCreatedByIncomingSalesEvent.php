<?php

namespace sales\events\sms;

use common\models\Sms;

/**
 * Class SmsCreatedByIncomingSalesEvent
 *
 * @property Sms $sms
 * @property int|null $leadId
 * @property string|null $clientPhone
 * @property string|null $userPhone
 * @property string|null $text
 */
class SmsCreatedByIncomingSalesEvent
{
    public $sms;
    public $leadId;
    public $clientPhone;
    public $userPhone;
    public $text;

    /**
     * @param Sms $sms
     * @param int|null $leadId
     * @param string|null $clientPhone
     * @param string|null $userPhone
     * @param string|null $text
     */
        public function __construct(Sms $sms, ?int $leadId, ?string $clientPhone, ?string $userPhone, ?string $text)
        {
            $this->sms = $sms;
            $this->leadId = $leadId;
            $this->clientPhone = $clientPhone;
            $this->userPhone = $userPhone;
            $this->text = $text;
        }
}
