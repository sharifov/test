<?php

namespace modules\lead\src\abac\dto;

class LeadExpertCallAbacDto extends \stdClass
{
    public int $leadStatus;
    public bool $hasFlightSegment;
    public int $quoteCount = 0;
    public int $smsCount = 0;
    public int $callCount = 0;
    public int $emailCount = 0;

    public bool $canMakeCall = false;
    public bool $canSendEmail = false;
    public bool $canSendSms = false;


    public function __construct(
        int $leadStatus,
        bool $hasFlightSegment,
        int $quoteCount,
        int $smsCount,
        int $callCount,
        int $emailCount,
        bool $canMakeCall,
        bool $canSendEmail,
        bool $canSendSms
    ) {
        $this->leadStatus = $leadStatus;
        $this->hasFlightSegment = $hasFlightSegment;
        $this->quoteCount = $quoteCount;
        $this->smsCount = $smsCount;
        $this->callCount = $callCount;
        $this->emailCount = $emailCount;
        $this->canMakeCall = $canMakeCall;
        $this->canSendEmail = $canSendEmail;
        $this->canSendSms = $canSendSms;
    }
}
