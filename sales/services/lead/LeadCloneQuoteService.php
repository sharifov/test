<?php

namespace sales\services\lead;

use common\models\Lead;
use common\models\LeadLog;
use common\models\local\LeadLogMessage;
use common\models\Quote;
use common\models\QuotePrice;
use common\models\QuoteSegment;
use common\models\QuoteSegmentBaggage;
use common\models\QuoteSegmentBaggageCharge;
use common\models\QuoteSegmentStop;
use common\models\QuoteTrip;
use sales\dispatchers\DeferredEventDispatcher;
use sales\events\lead\LeadQuoteCloneEvent;
use sales\repositories\lead\LeadRepository;
use sales\repositories\quote\QuotePriceRepository;
use sales\repositories\quote\QuoteRepository;
use sales\repositories\quote\QuoteSegmentBaggageChargeRepository;
use sales\repositories\quote\QuoteSegmentBaggageRepository;
use sales\repositories\quote\QuoteSegmentRepository;
use sales\repositories\quote\QuoteSegmentStopRepository;
use sales\repositories\quote\QuoteTripRepository;
use sales\services\TransactionManager;
use yii\helpers\VarDumper;

/**
 * Class LeadCloneQuoteService
 *
 * @property LeadRepository $leadRepository,
 * @property QuoteRepository $quoteRepository
 * @property QuotePriceRepository $quotePriceRepository
 * @property QuoteTripRepository $quoteTripRepository
 * @property QuoteSegmentRepository $quoteSegmentRepository
 * @property QuoteSegmentStopRepository $quoteSegmentStopRepository
 * @property QuoteSegmentBaggageRepository $quoteSegmentBaggageRepository
 * @property QuoteSegmentBaggageChargeRepository $quoteSegmentBaggageChargeRepository
 * @property TransactionManager $transactionManager
 * @property DeferredEventDispatcher $eventDispatcher
 */
class LeadCloneQuoteService
{

    private $leadRepository;
    private $quoteRepository;
    private $quotePriceRepository;
    private $quoteTripRepository;
    private $quoteSegmentRepository;
    private $quoteSegmentStopRepository;
    private $quoteSegmentBaggageRepository;
    private $quoteSegmentBaggageChargeRepository;
    private $transactionManager;
    private $eventDispatcher;

    public function __construct(
        LeadRepository $leadRepository,
        QuoteRepository $quoteRepository,
        QuotePriceRepository $quotePriceRepository,
        QuoteTripRepository $quoteTripRepository,
        QuoteSegmentRepository $quoteSegmentRepository,
        QuoteSegmentStopRepository $quoteSegmentStopRepository,
        QuoteSegmentBaggageRepository $quoteSegmentBaggageRepository,
        QuoteSegmentBaggageChargeRepository $quoteSegmentBaggageChargeRepository,
        TransactionManager $transactionManager,
        DeferredEventDispatcher $eventDispatcher
    )
    {
        $this->quoteRepository = $quoteRepository;
        $this->leadRepository = $leadRepository;
        $this->quotePriceRepository = $quotePriceRepository;
        $this->quoteTripRepository = $quoteTripRepository;
        $this->quoteSegmentRepository = $quoteSegmentRepository;
        $this->quoteSegmentStopRepository = $quoteSegmentStopRepository;
        $this->quoteSegmentBaggageRepository = $quoteSegmentBaggageRepository;
        $this->quoteSegmentBaggageChargeRepository = $quoteSegmentBaggageChargeRepository;
        $this->transactionManager = $transactionManager;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @param string $quoteUid
     * @param string $leadGid
     * @throws \Throwable
     * @throws \yii\base\InvalidConfigException
     */
    public function cloneByUid(string $quoteUid, string $leadGid): void
    {
        $currentQuote = $this->quoteRepository->findByUid($quoteUid);
        $lead = $this->leadRepository->findByGid($leadGid);

        if (!$leadQuote = $currentQuote->lead) {
            throw new \DomainException('Not found Lead in this quote');
        }

        self::guardSegments($leadQuote, $lead);
        self::guardTypeCabin($leadQuote, $lead);
        self::guardTypePassengers($currentQuote, $lead);
        self::guardCountPassengers($currentQuote, $lead);

        $result = $this->transactionManager->wrap(function () use ($currentQuote, $lead) {

            $quote = Quote::cloneByUid($currentQuote->attributes, $lead->id, $lead->originalQuoteExist());
            $this->quoteRepository->save($quote);

            $selling = $this->cloneQuotePrices($currentQuote, $lead, $quote->id);

            foreach ($currentQuote->quoteTrips as $trip) {
                $this->cloneTrips($trip, $quote->id);
            }

            return ['quote' => $quote, 'selling' => $selling];

        });

        /** @var Quote $quote */
        $quote = $result['quote'];
        $selling = $result['selling'];

        if ($lead->called_expert) {
            $this->eventDispatcher->dispatch(new LeadQuoteCloneEvent($quote));
        }

        // todo
//        $oldParams = $currentQuote->attributes;
//        $oldParams['selling'] = '';
//
//        $newParams = $quote->attributes;
//        $newParams['selling'] = round($selling, 2);
//
//        $message = new LeadLogMessage([
//            'title' => 'Created ' . $quote->id . ' (Clone from ' . $currentQuote->id . ')',
//            'model' => $quote->formName() . ' (' . $quote->uid . ')',
//            'oldParams' => $oldParams,
//            'newParams' => $newParams
//        ]);
//        $leadLog = new LeadLog($message);
//        $leadLog->addLog([
//            'lead_id' => $quote->lead_id,
//        ]);

    }

    /**
     * @param int $count
     * @param array $attributes
     * @param int $quoteId
     * @return float
     */
    private function cloneQuotePrice(int $count, array $attributes, int $quoteId): float
    {
        $selling = 0;
        for ($i = 0; $i < $count; $i++) {
            $newPrice = QuotePrice::clone($attributes, $quoteId);
            $selling += $newPrice->selling;
            $this->quotePriceRepository->save($newPrice);
        }
        return $selling;
    }

    /**
     * @param Quote $currentQuote
     * @param Lead $lead
     * @param int $quoteId
     * @return float
     */
    private function cloneQuotePrices(Quote $currentQuote, Lead $lead, $quoteId): float
    {
        $selling = 0;

        $adult = false;
        $children = false;
        $infant = false;

        foreach ($currentQuote->quotePrices as $price) {
            if (!$adult && $price->isAdult()) {
                $selling += $this->cloneQuotePrice($lead->adults, $price->attributes, $quoteId);
                $adult = true;
            }
            if (!$children && $price->isChild()) {
                $selling += $this->cloneQuotePrice($lead->children, $price->attributes, $quoteId);
                $children = true;
            }
            if (!$infant && $price->isInfant()) {
                $selling += $this->cloneQuotePrice($lead->infants, $price->attributes, $quoteId);
                $infant = true;
            }
        }

        return $selling;
    }

    /**
     * @param QuoteTrip $trip
     * @param int $quoteId
     */
    private function cloneTrips(QuoteTrip $trip, int $quoteId): void
    {
        $newTrip = QuoteTrip::clone($trip->attributes, $quoteId);
        $this->quoteTripRepository->save($newTrip);

        foreach ($trip->quoteSegments as $segment) {

            $newSegment = QuoteSegment::clone($segment->attributes, $newTrip->qt_id);
            $this->quoteSegmentRepository->save($newSegment);

            foreach ($segment->quoteSegmentStops as $stop) {
                $newStop = QuoteSegmentStop::clone($stop->attributes, $newSegment->qs_id);
                $this->quoteSegmentStopRepository->save($newStop);
            }

            foreach ($segment->quoteSegmentBaggages as $baggage) {
                $newBaggage = QuoteSegmentBaggage::clone($baggage->attributes, $newSegment->qs_id);
                $this->quoteSegmentBaggageRepository->save($newBaggage);
            }

            foreach ($segment->quoteSegmentBaggageCharges as $baggage) {
                $newBaggage = QuoteSegmentBaggageCharge::clone($baggage->attributes, $newSegment->qs_id);
                $this->quoteSegmentBaggageChargeRepository->save($newBaggage);
            }

        }
    }

    /**
     * @param Lead $leadQuote
     * @param Lead $lead
     */
    public static function guardSegments(Lead $leadQuote, Lead $lead): void
    {
        $leadQuoteSegments = [];
        foreach ($leadQuote->leadFlightSegments as $segment) {
            $leadQuoteSegments[] = [
                'origin' => $segment->origin,
                'destination' => $segment->destination,
                'departure' => $segment->departure
            ];
        }

        if (!$lead->equalsSegments($leadQuoteSegments)) {
            throw new \DomainException('Different segments');

        }
    }

    /**
     * @param Lead $leadQuote
     * @param Lead $lead
     */
    public static function guardTypeCabin(Lead $leadQuote, Lead $lead): void
    {
        if ($lead->cabin != $leadQuote->cabin) {
            throw new \DomainException('Only same cabin type can be cloned.');
        }
    }

    /**
     * @param Quote $quote
     * @param Lead $lead
     */
    public static function guardTypePassengers(Quote $quote, Lead $lead): void
    {
        $ADT = 0;
        $CHD = 0;
        $INF = 0;

        foreach ($quote->quotePrices as $price) {
            if ($price->isAdult()) {
                $ADT++;
            } elseif ($price->isChild()) {
                $CHD++;
            } elseif ($price->isInfant()) {
                $INF++;
            }
        }

        if (($lead->adults && !$ADT) || ($lead->children && !$CHD) || ($lead->infants && !$INF)) {
            throw new \DomainException('Only same passengers type can be cloned. Please use Create Quote and Quote Dump instead');
        }
    }

    /**
     * @param Quote $quote
     * @param Lead $lead
     */
    public static function guardCountPassengers(Quote $quote, Lead $lead): void
    {
        $ADT = 0;
        $CHD = 0;

        foreach ($quote->quotePrices as $price) {
            if ($price->isAdult()) {
                $ADT++;
            } elseif ($price->isChild()) {
                $CHD++;
            }
        }

        if ($lead->adults > $ADT || $lead->children > $CHD) {
            $newCount = $lead->adults + $lead->children;
            if ($newCount > 4) {
                throw new \DomainException('Can\'t clone the quote with new number of passengers (' . $newCount . '). Max. 4 passengers allowed. (adults + children without infants)');
            }
        }
    }

}
