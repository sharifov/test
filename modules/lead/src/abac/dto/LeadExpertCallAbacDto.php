<?php

namespace modules\lead\src\abac\dto;

class LeadExpertCallAbacDto extends \stdClass
{
    public int $leadStatus;
    public bool $hasFlightSegment;
    public int $quoteCount = 0;
    public int $callCount = 0;
    public bool $canMakeCall = false;
    public ?int $leadTypeId = null;


    public function __construct(
        int $leadStatus,
        bool $hasFlightSegment,
        int $quoteCount,
        int $callCount,
        bool $canMakeCall,
        ?int $leadTypeId = null
    ) {
        $this->leadStatus = $leadStatus;
        $this->hasFlightSegment = $hasFlightSegment;
        $this->quoteCount = $quoteCount;
        $this->callCount = $callCount;
        $this->canMakeCall = $canMakeCall;
        $this->leadTypeId = $leadTypeId;
    }
}
