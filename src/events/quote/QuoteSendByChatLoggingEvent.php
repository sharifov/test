<?php

namespace src\events\quote;

/**
 * Class QuoteSendByChatLoggingEvent
 * @package src\events\quote
 */
class QuoteSendByChatLoggingEvent
{
    /** @var int $chatId */
    public $chatId;
    /** @var int $quoteId */
    public $quoteId;

    /**
     * QuoteSendByChat constructor.
     * @param int $quoteId
     * @param int $chatId
     */
    public function __construct(int $quoteId, int $chatId)
    {
        $this->quoteId = $quoteId;
        $this->chatId = $chatId;
    }
}
