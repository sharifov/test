<?php

namespace src\events\quote;

/**
 * Class QuoteSendBySmsLoggingEvent
 * @package src\events\quote
 */
class QuoteSendBySmsLoggingEvent
{
    /** @var int $smsId */
    public $smsId;
    /** @var int $quoteId */
    public $quoteId;

    /**
     * QuoteSendBySms constructor.
     * @param int $quoteId
     * @param int $smsId
     */
    public function __construct(int $quoteId, int $smsId)
    {
        $this->quoteId = $quoteId;
        $this->smsId = $smsId;
    }
}
