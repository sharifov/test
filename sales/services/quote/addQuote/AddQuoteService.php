<?php

namespace sales\services\quote\addQuote;

use common\models\Employee;
use common\models\Lead;
use common\models\Quote;
use common\models\QuotePrice;
use common\models\QuoteSegment;
use common\models\QuoteSegmentBaggage;
use common\models\QuoteSegmentBaggageCharge;
use common\models\QuoteSegmentStop;
use common\models\QuoteTrip;
use sales\helpers\setting\SettingHelper;
use sales\model\clientChat\socket\ClientChatSocketCommands;
use sales\model\clientChatLead\entity\ClientChatLead;
use sales\repositories\quote\QuotePriceRepository;
use sales\repositories\quote\QuoteRepository;
use sales\repositories\quote\QuoteSegmentBaggageChargeRepository;
use sales\repositories\quote\QuoteSegmentBaggageRepository;
use sales\repositories\quote\QuoteSegmentRepository;
use sales\repositories\quote\QuoteSegmentStopRepository;
use sales\repositories\quote\QuoteTripRepository;
use sales\services\TransactionManager;

/**
 * Class AddQuoteService
 * @package sales\services\quote\addQuote
 *
 * @property TransactionManager $transactionManager
 * @property QuoteRepository $quoteRepository
 * @property QuoteTripRepository $quoteTripRepository
 * @property QuoteSegmentRepository $quoteSegmentRepository
 * @property QuoteSegmentStopRepository $quoteSegmentStopRepository
 * @property QuoteSegmentBaggageRepository $quoteSegmentBaggageRepository
 * @property QuoteSegmentBaggageChargeRepository $quoteSegmentBaggageChargeRepository
 * @property QuotePriceRepository $quotePriceRepository
 * @property array $tripKey
 */
class AddQuoteService
{
    /**
     * @var TransactionManager
     */
    private TransactionManager $transactionManager;
    /**
     * @var QuoteRepository
     */
    private QuoteRepository $quoteRepository;
    /**
     * @var QuoteTripRepository
     */
    private QuoteTripRepository $quoteTripRepository;

    /**
     * @var QuoteSegmentRepository
     */
    private QuoteSegmentRepository $quoteSegmentRepository;

    /**
     * @var QuoteSegmentStopRepository
     */
    private QuoteSegmentStopRepository $quoteSegmentStopRepository;

    /**
     * @var QuoteSegmentBaggageRepository
     */
    private QuoteSegmentBaggageRepository $quoteSegmentBaggageRepository;

    /**
     * @var QuoteSegmentBaggageChargeRepository
     */
    private QuoteSegmentBaggageChargeRepository $quoteSegmentBaggageChargeRepository;

    /**
     * @var QuotePriceRepository
     */
    private QuotePriceRepository $quotePriceRepository;

    private array $tripKey = [];

    public function __construct(
        TransactionManager $transactionManager,
        QuoteRepository $quoteRepository,
        QuoteTripRepository $quoteTripRepository,
        QuoteSegmentRepository $quoteSegmentRepository,
        QuoteSegmentStopRepository $quoteSegmentStopRepository,
        QuoteSegmentBaggageRepository $quoteSegmentBaggageRepository,
        QuoteSegmentBaggageChargeRepository $quoteSegmentBaggageChargeRepository,
        QuotePriceRepository $quotePriceRepository
    ) {
        $this->transactionManager = $transactionManager;
        $this->quoteRepository = $quoteRepository;
        $this->quoteTripRepository = $quoteTripRepository;
        $this->quoteSegmentRepository = $quoteSegmentRepository;
        $this->quoteSegmentStopRepository = $quoteSegmentStopRepository;
        $this->quoteSegmentBaggageRepository = $quoteSegmentBaggageRepository;
        $this->quoteSegmentBaggageChargeRepository = $quoteSegmentBaggageChargeRepository;
        $this->quotePriceRepository = $quotePriceRepository;
    }

    public function createQuoteFromSearch(array $quoteData, Lead $lead, Employee $employee)
    {
        return $this->transactionManager->wrap(function () use ($quoteData, $lead, $employee) {
            $quote = Quote::createQuoteFromSearch($quoteData, $lead, $employee);
            $this->quoteRepository->save($quote);

            $this->createQuoteTripsFromSearch($quoteData['trips'] ?? [], $quote);
            $this->createQuotePriceFromSearch($quoteData['passengers'] ?? [], $quote);
            if ($lead->called_expert) {
                $quote->sendUpdateBO();
            }

            $chat = ClientChatLead::find()->andWhere(['ccl_lead_id' => $lead->id])->one();
            if ($chat) {
                ClientChatSocketCommands::clientChatAddQuotesButton($chat->chat, $lead->id);
            }
        });
    }

    public function autoSelectQuotes(array $quotes, Lead $lead, Employee $employee): void
    {
        $autoSelectQuoteCount = SettingHelper::getFlightQuoteAutoSelectCount();
        $i = 0;
        foreach ($quotes as $quote) {
            if ($i === $autoSelectQuoteCount) {
                break;
            }
            $this->createQuoteFromSearch($quote, $lead, $employee);
            $i++;
        }
    }

    private function createQuoteTripsFromSearch(array $trips, Quote $quote): void
    {
        $quoteTicketsSegments = $quote->getTicketSegments();
        foreach ($trips as $tripKey => $trip) {
            $quoteTrip = new QuoteTrip();
            $quoteTrip->qt_duration = $trip['duration'] ?? null;
            $quote->link('quoteTrips', $quoteTrip);

            $tripNr = $tripKey + 1;
            $this->createQuoteSegmentsFromSearch($trip['segments'] ?? [], $quoteTrip, $quoteTicketsSegments[$tripNr] ?? []);

            $quoteTrip->qt_key = implode('|', $this->tripKey);
            $this->quoteTripRepository->save($quoteTrip);
            $this->tripKey = [];
        }
    }

    private function createQuoteSegmentsFromSearch(array $segments, QuoteTrip $quoteTrip, array $quoteTicketsSegment): void
    {
        $segmentNr = 1;
        foreach ($segments as $segment) {
            $quoteSegment = QuoteSegment::createFromSearch($segment, $quoteTicketsSegment[$segmentNr] ?? null);
            $this->quoteSegmentRepository->save($quoteSegment);
            $this->tripKey[] = $quoteSegment->qs_key;
            $quoteTrip->link('quoteSegments', $quoteSegment);

            $this->createQuoteSegmentsStopsFromSearch($segment['stops'] ?? [], $quoteSegment);
            $this->createQuoteSegmentBaggagesFromSearch($segment['baggage'] ?? [], $quoteSegment);

            $segmentNr++;
        }
    }

    private function createQuoteSegmentsStopsFromSearch(array $segmentStops, QuoteSegment $quoteSegment): void
    {
        foreach ($segmentStops as $stopEntry) {
            $quoteSegmentStop = QuoteSegmentStop::createFromSearch($stopEntry);
            $this->quoteSegmentStopRepository->save($quoteSegmentStop);
            $quoteSegment->link('quoteSegmentStops', $quoteSegmentStop);
        }
    }

    private function createQuoteSegmentBaggagesFromSearch(array $baggage, QuoteSegment $quoteSegment): void
    {
        foreach ($baggage as $paxCode => $baggageEntry) {
            $quoteSegmentBaggage = QuoteSegmentBaggage::createFromSearch($baggageEntry, (string)$paxCode);
            $this->quoteSegmentBaggageRepository->save($quoteSegmentBaggage);
            $quoteSegment->link('quoteSegmentBaggages', $quoteSegmentBaggage);

            foreach ($baggageEntry['charge'] ?? [] as $baggageEntryCharge) {
                $quoteSegmentBaggageCharge = QuoteSegmentBaggageCharge::createFromSearch($baggageEntryCharge, (string)$paxCode);
                $this->quoteSegmentBaggageChargeRepository->save($quoteSegmentBaggageCharge);
                $quoteSegment->link('quoteSegmentBaggageCharges', $quoteSegmentBaggageCharge);
            }
        }
    }

    private function createQuotePriceFromSearch(array $passengers, Quote $quote): void
    {
        foreach ($passengers as $paxCode => $paxEntry) {
            for ($i = 0; $i < $paxEntry['cnt']; $i++) {
                $quotePrice = QuotePrice::createFromSearch(
                    $paxEntry,
                    (string)$paxCode,
                    (bool)$quote->check_payment,
                    (new Quote())->serviceFeePercent
                );
                $this->quotePriceRepository->save($quotePrice);
                $quote->link('quotePrices', $quotePrice);
            }
        }
    }
}
