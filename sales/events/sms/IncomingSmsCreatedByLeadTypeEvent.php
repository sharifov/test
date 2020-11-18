<?php

namespace sales\events\sms;

use common\models\Sms;

/**
 * Class IncomingSmsCreatedByLeadTypeEvent
 *
 * @property Sms $sms
 * @property int|null $leadId
 * @property string|null $clientPhone
 * @property string|null $userPhone
 * @property string|null $text
 */
class IncomingSmsCreatedByLeadTypeEvent
{
    public $sms;
    public $leadId;
    public $clientPhone;
    public $userPhone;
    public $text;

    public function __construct(Sms $sms, ?int $leadId, ?string $clientPhone, ?string $userPhone, ?string $text)
    {
        $this->sms = $sms;
        $this->leadId = $leadId;
        $this->clientPhone = $clientPhone;
        $this->userPhone = $userPhone;
        $this->text = $text;
    }
}
