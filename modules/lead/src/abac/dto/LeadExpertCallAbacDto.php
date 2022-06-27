<?php

namespace modules\lead\src\abac\dto;

class LeadExpertCallAbacDto extends \stdClass
{
    public int $leadStatus;
    public bool $hasFlightSegment;
    public int $quoteCount = 0;


    public function __construct(
        int $leadStatus,
        bool $hasFlightSegment,
        int $quoteCount
    ) {
        $this->leadStatus = $leadStatus;
        $this->hasFlightSegment = $hasFlightSegment;
        $this->quoteCount = $quoteCount;
    }
}
