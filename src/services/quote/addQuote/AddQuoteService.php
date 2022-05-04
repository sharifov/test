<?php

namespace src\services\quote\addQuote;

use common\models\Currency;
use common\models\Employee;
use common\models\Lead;
use common\models\Quote;
use common\models\QuotePrice;
use common\models\QuoteSegment;
use common\models\QuoteSegmentBaggage;
use common\models\QuoteSegmentBaggageCharge;
use common\models\QuoteSegmentStop;
use common\models\QuoteTrip;
use src\helpers\app\AppHelper;
use src\helpers\ErrorsToStringHelper;
use src\helpers\setting\SettingHelper;
use src\model\clientChat\socket\ClientChatSocketCommands;
use src\model\clientChatLead\entity\ClientChatLead;
use src\model\quoteLabel\entity\QuoteLabel;
use src\model\quoteLabel\repository\QuoteLabelRepository;
use src\model\quoteLabel\service\QuoteLabelService;
use src\repositories\quote\QuotePriceRepository;
use src\repositories\quote\QuoteRepository;
use src\repositories\quote\QuoteSegmentBaggageChargeRepository;
use src\repositories\quote\QuoteSegmentBaggageRepository;
use src\repositories\quote\QuoteSegmentRepository;
use src\repositories\quote\QuoteSegmentStopRepository;
use src\repositories\quote\QuoteTripRepository;
use src\services\quote\addQuote\price\QuotePriceCreateService;
use src\services\quote\addQuote\price\QuotePriceSearchForm;
use src\services\TransactionManager;
use yii\helpers\ArrayHelper;

/**
 * Class AddQuoteService
 * @package src\services\quote\addQuote
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

    public function createQuoteFromSearch(array $quoteData, Lead $lead, ?Employee $employee)
    {
        return $this->transactionManager->wrap(function () use ($quoteData, $lead, $employee) {
            if (!$currencyCode = $quoteData['currency'] ?? null) {
                throw new \RuntimeException('Currency not exist in search result');
            }
            if (!$currency = Currency::find()->byCode((string) $currencyCode)->limit(1)->one()) {
                throw new \RuntimeException('Currency not found by code(' . $currencyCode . ')');
            }

            $quote = Quote::createQuoteFromSearch($quoteData, $lead, $employee, $currency);
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

            try {
                QuoteLabelService::processingQuoteLabel($quoteData, $quote->id);
            } catch (\Throwable $throwable) {
                \Yii::warning($throwable->getMessage(), 'AddQuoteService:createQuoteFromSearch:QuoteLabel');
            }
        });
    }

    public function createByData(array $data, Lead $lead, ?int $providerProjectId): string
    {
        return $this->transactionManager->wrap(function () use ($data, $lead, $providerProjectId) {
            $quote = Quote::createQuoteFromSearch($data, $lead, null, null);
            $quote->provider_project_id = $providerProjectId;
            $this->quoteRepository->save($quote);

            $this->createQuoteTripsFromSearch($data['trips'] ?? [], $quote);
            $this->createQuotePriceFromSearch($data['passengers'] ?? [], $quote);
            if ($lead->called_expert) {
                $quote->sendUpdateBO();
            }

            return $quote->uid;
        });
    }

    public function autoSelectQuotes(array $quotes, Lead $lead, ?Employee $employee, bool $isReverse = false): void
    {
        if ($isReverse) {
            $quotes = array_reverse($quotes);
        }
        foreach ($quotes as $quote) {
            $quote['createTypeId'] = Quote::CREATE_TYPE_AUTO_SELECT;
            $this->createQuoteFromSearch($quote, $lead, $employee);
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
            if (!$quoteSegment->validate()) {
                throw new \RuntimeException(ErrorsToStringHelper::extractFromModel($quoteSegment, ' '));
            }

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
                try {
                    $quotePriceSearchForm = new QuotePriceSearchForm(
                        $paxCode,
                        (bool) $quote->check_payment,
                        $quote->q_client_currency,
                        $quote
                    );
                    if (!$quotePriceSearchForm->load($paxEntry)) {
                        throw new \RuntimeException('QuotePriceSearchForm not loaded');
                    }
                    if (!$quotePriceSearchForm->validate()) {
                        throw new \RuntimeException(ErrorsToStringHelper::extractFromModel($quotePriceSearchForm));
                    }

                    $quotePrice = QuotePriceCreateService::createFromSearch($quotePriceSearchForm);
                    if (!$quotePrice->validate()) {
                        throw new \RuntimeException(ErrorsToStringHelper::extractFromModel($quotePrice, ' '));
                    }
                    $quote->link('quotePrices', $quotePrice);
                } catch (\Throwable $throwable) {
                    $message = ArrayHelper::merge(AppHelper::throwableLog($throwable), $paxEntry);
                    \Yii::error($message, 'AddQuoteService:createQuotePriceFromSearch:Throwable');
                    throw $throwable;
                }
            }
        }
    }
}
