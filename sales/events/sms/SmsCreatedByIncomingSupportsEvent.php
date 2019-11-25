<?php

namespace sales\events\sms;

use common\models\Sms;

/**
 * Class SmsCreatedByIncomingSupportsEvent
 *
 * @property Sms $sms
 * @property int|null $caseId
 * @property string|null $clientPhone
 * @property string|null $userPhone
 * @property string|null $text
 */
class SmsCreatedByIncomingSupportsEvent
{
    public $sms;
    public $caseId;
    public $clientPhone;
    public $userPhone;
    public $text;

    /**
     * @param Sms $sms
     * @param int|null $caseId
     * @param string|null $clientPhone
     * @param string|null $userPhone
     * @param string|null $text
     */
    public function __construct(Sms $sms, ?int $caseId, ?string $clientPhone, ?string $userPhone, ?string $text)
    {
        $this->sms = $sms;
        $this->caseId = $caseId;
        $this->clientPhone = $clientPhone;
        $this->userPhone = $userPhone;
        $this->text = $text;
    }
}
