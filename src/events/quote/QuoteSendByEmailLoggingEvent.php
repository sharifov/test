<?php

namespace src\events\quote;

/**
 * Class QuoteSendByEmailLoggingEvent
 * @package src\events\quote
 */
class QuoteSendByEmailLoggingEvent
{
    /** @var int $emailId */
    public $emailId;
    /** @var int $quoteId */
    public $quoteId;

    /**
     * QuoteSendByEmail constructor.
     * @param int $quoteId
     * @param int $emailId
     */
    public function __construct(int $quoteId, int $emailId)
    {
        $this->quoteId = $quoteId;
        $this->emailId = $emailId;
    }
}
