<?php

namespace sales\listeners\lead;

use sales\events\lead\LeadCountPassengersChangedEvent;
use sales\repositories\quote\QuoteRepository;

/**
 * Class LeadPassengersChangedEventListener
 * @property QuoteRepository $quoteRepository
 */
class LeadPassengersChangedEventListener
{

    private $quoteRepository;

    /**
     * LeadPassengersChangedEventListener constructor.
     * @param QuoteRepository $quoteRepository
     */
    public function __construct(QuoteRepository $quoteRepository)
    {
        $this->quoteRepository = $quoteRepository;
    }

    /**
     * @param LeadCountPassengersChangedEvent $event
     */
    public function handle(LeadCountPassengersChangedEvent $event): void
    {
        foreach ($event->lead->getAltQuotes() as $quote) {
            if (!$quote->isApplied()) {
                $quote->decline();
                try {
                    $this->quoteRepository->save($quote);
                } catch (\Exception $e) {
                    \Yii::$app->errorHandler->logException($e);
                }
            }
        }
    }

}