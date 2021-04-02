<?php

namespace modules\flight\src\useCases\flightQuote;

use common\models\Lead;
use modules\flight\models\FlightPax;
use modules\flight\models\FlightQuoteStatusLog;
use modules\flight\models\FlightSegment;
use modules\flight\models\forms\ItineraryEditForm;
use modules\flight\src\dto\flightSegment\SegmentDTO;
use modules\flight\src\dto\itineraryDump\ItineraryDumpDTO;
use modules\flight\src\exceptions\FlightCodeException;
use modules\flight\src\repositories\flight\FlightRepository;
use modules\flight\src\repositories\flightQuoteStatusLogRepository\FlightQuoteStatusLogRepository;
use modules\flight\src\repositories\flightSegment\FlightSegmentRepository;
use modules\flight\src\services\flightQuote\FlightQuotePriceCalculator;
use modules\flight\src\useCases\api\searchQuote\FlightQuoteSearchHelper;
use modules\flight\src\useCases\flightQuote\create\FlightPaxDTO;
use modules\flight\src\useCases\flightQuote\createManually\FlightQuoteCreateForm;
use modules\flight\src\useCases\flightQuote\createManually\FlightQuotePaxPriceForm;
use modules\offer\src\entities\offerProduct\OfferProduct;
use modules\offer\src\services\OfferPriceUpdater;
use modules\order\src\exceptions\OrderC2BDtoException;
use modules\order\src\exceptions\OrderC2BException;
use modules\order\src\forms\api\createC2b\QuotesForm;
use modules\order\src\services\OrderPriceUpdater;
use modules\product\src\entities\product\ProductRepository;
use modules\product\src\entities\productHolder\ProductHolder;
use modules\product\src\entities\productHolder\ProductHolderRepository;
use modules\product\src\entities\productOption\ProductOptionRepository;
use modules\product\src\entities\productQuote\events\ProductQuoteRecalculateProfitAmountEvent;
use modules\product\src\entities\productQuote\ProductQuote;
use modules\flight\models\Flight;
use modules\flight\models\FlightQuote;
use modules\flight\models\FlightQuotePaxPrice;
use modules\flight\models\FlightQuoteSegment;
use modules\flight\models\FlightQuoteSegmentPaxBaggage;
use modules\flight\models\FlightQuoteSegmentPaxBaggageCharge;
use modules\flight\models\FlightQuoteSegmentStop;
use modules\flight\models\FlightQuoteTrip;
use modules\flight\src\helpers\FlightQuoteHelper;
use modules\flight\src\repositories\flightPaxRepository\FlightPaxRepository;
use modules\flight\src\repositories\flightQuotePaxPriceRepository\FlightQuotePaxPriceRepository;
use modules\flight\src\repositories\flightQuoteRepository\FlightQuoteRepository;
use modules\flight\src\repositories\flightQuoteSegment\FlightQuoteSegmentRepository;
use modules\flight\src\repositories\FlightQuoteSegmentPaxBaggageChargeRepository\FlightQuoteSegmentPaxBaggageChargeRepository;
use modules\flight\src\repositories\flightQuoteSegmentPaxBaggageRepository\FlightQuoteSegmentPaxBaggageRepository;
use modules\flight\src\repositories\flightQuoteSegmentStopRepository\FlightQuoteSegmentStopRepository;
use modules\flight\src\repositories\flightQuoteTripRepository\FlightQuoteTripRepository;
use modules\flight\src\useCases\flightQuote\create\FlightQuoteCreateDTO;
use modules\flight\src\useCases\flightQuote\create\FlightQuotePaxPriceDTO;
use modules\flight\src\useCases\flightQuote\create\FlightQuoteSegmentDTO;
use modules\flight\src\useCases\flightQuote\create\FlightQuoteSegmentPaxBaggageChargeDTO;
use modules\flight\src\useCases\flightQuote\create\FlightQuoteSegmentPaxBaggageDTO;
use modules\flight\src\useCases\flightQuote\create\FlightQuoteSegmentStopDTO;
use modules\flight\src\useCases\flightQuote\create\ProductQuoteCreateDTO;
use modules\product\src\entities\productQuote\ProductQuoteStatus;
use modules\product\src\entities\productQuoteOption\ProductQuoteOption;
use modules\product\src\entities\productQuoteOption\ProductQuoteOptionRepository;
use modules\product\src\entities\productType\ProductType;
use modules\product\src\interfaces\Productable;
use modules\product\src\interfaces\ProductQuoteService;
use sales\repositories\product\ProductQuoteRepository;
use sales\services\TransactionManager;
use yii\helpers\Json;
use yii\helpers\VarDumper;

/**
 * Class FlightQuoteManageService
 * @package modules\flight\src\useCases\flightQuote
 *
 * @property FlightQuoteRepository $flightQuoteRepository
 * @property TransactionManager $transactionManager
 * @property ProductQuoteRepository $productQuoteRepository
 * @property FlightQuoteTripRepository $flightQuoteTripRepository
 * @property FlightQuoteSegmentRepository $flightQuoteSegmentRepository
 * @property FlightQuoteSegmentStopRepository $flightQuoteSegmentStopRepository
 * @property FlightQuoteSegmentPaxBaggageRepository $flightQuoteSegmentPaxBaggageRepository
 * @property FlightPaxRepository $flightPaxRepository
 * @property FlightQuoteSegmentPaxBaggageChargeRepository $baggageChargeRepository
 * @property FlightQuotePaxPriceRepository $flightQuotePaxPriceRepository
 * @property FlightQuoteStatusLogRepository $flightQuoteStatusLogRepository
 * @property OrderPriceUpdater $orderPriceUpdater
 * @property ProductHolderRepository $productHolderRepository
 * @property ProductOptionRepository $productOptionRepository
 * @property ProductQuoteOptionRepository $productQuoteOptionRepository
 */
class FlightQuoteManageService implements ProductQuoteService
{
    /**
     * @var FlightQuoteRepository
     */
    private $flightQuoteRepository;
    /**
     * @var TransactionManager
     */
    private $transactionManager;
    /**
     * @var ProductQuoteRepository
     */
    private $productQuoteRepository;
    /**
     * @var FlightQuoteTripRepository
     */
    private $flightQuoteTripRepository;
    /**
     * @var FlightQuoteSegmentRepository
     */
    private $flightQuoteSegmentRepository;
    /**
     * @var FlightQuoteSegmentStopRepository
     */
    private $flightQuoteSegmentStopRepository;
    /**
     * @var FlightQuoteSegmentPaxBaggageRepository
     */
    private $flightQuoteSegmentPaxBaggageRepository;
    /**
     * @var FlightPaxRepository
     */
    private $flightPaxRepository;
    /**
     * @var FlightQuoteSegmentPaxBaggageChargeRepository
     */
    private $baggageChargeRepository;
    /**
     * @var FlightQuotePaxPriceRepository
     */
    private $flightQuotePaxPriceRepository;
    /**
     * @var FlightQuoteStatusLogRepository
     */
    private $flightQuoteStatusLogRepository;
    /**
     * @var OrderPriceUpdater
     */
    private OrderPriceUpdater $orderPriceUpdater;
    /**
     * @var OfferPriceUpdater
     */
    private OfferPriceUpdater $offerPriceUpdater;
    /**
     * @var ProductHolderRepository
     */
    private ProductHolderRepository $productHolderRepository;
    /**
     * @var ProductOptionRepository
     */
    private ProductOptionRepository $productOptionRepository;
    /**
     * @var ProductQuoteOptionRepository
     */
    private ProductQuoteOptionRepository $productQuoteOptionRepository;

    public function __construct(
        FlightQuoteRepository $flightQuoteRepository,
        ProductQuoteRepository $productQuoteRepository,
        FlightPaxRepository $flightPaxRepository,
        FlightQuoteTripRepository $flightQuoteTripRepository,
        FlightQuoteSegmentRepository $flightQuoteSegmentRepository,
        FlightQuoteSegmentStopRepository $flightQuoteSegmentStopRepository,
        FlightQuoteSegmentPaxBaggageRepository $flightQuoteSegmentPaxBaggageRepository,
        FlightQuoteSegmentPaxBaggageChargeRepository $baggageChargeRepository,
        FlightQuotePaxPriceRepository $flightQuotePaxPriceRepository,
        FlightQuoteStatusLogRepository $flightQuoteStatusLogRepository,
        TransactionManager $transactionManager,
        OrderPriceUpdater $orderPriceUpdater,
        ProductHolderRepository $productHolderRepository,
        ProductOptionRepository $productOptionRepository,
        ProductQuoteOptionRepository $productQuoteOptionRepository
    ) {
        $this->flightQuoteRepository = $flightQuoteRepository;
        $this->productQuoteRepository = $productQuoteRepository;
        $this->flightPaxRepository = $flightPaxRepository;
        $this->flightQuoteTripRepository = $flightQuoteTripRepository;
        $this->flightQuoteSegmentRepository = $flightQuoteSegmentRepository;
        $this->flightQuoteSegmentStopRepository = $flightQuoteSegmentStopRepository;
        $this->flightQuoteSegmentPaxBaggageRepository = $flightQuoteSegmentPaxBaggageRepository;
        $this->baggageChargeRepository = $baggageChargeRepository;
        $this->flightQuotePaxPriceRepository = $flightQuotePaxPriceRepository;
        $this->flightQuoteStatusLogRepository = $flightQuoteStatusLogRepository;
        $this->transactionManager = $transactionManager;
        $this->orderPriceUpdater = $orderPriceUpdater;
        $this->productHolderRepository = $productHolderRepository;
        $this->productOptionRepository = $productOptionRepository;
        $this->productQuoteOptionRepository = $productQuoteOptionRepository;
    }

    /**
     * @param Flight $flight
     * @param array $quote
     * @param int $userId
     * @throws \Throwable
     */
    public function create(Flight $flight, array $quote, int $userId): void
    {
        $productTypeServiceFee = null;
        $productType = ProductType::find()->select(['pt_service_fee_percent'])->byFlight()->asArray()->one();
        if ($productType && $productType['pt_service_fee_percent']) {
            $productTypeServiceFee = $productType['pt_service_fee_percent'];
        }

        $this->transactionManager->wrap(function () use ($flight, $quote, $userId, $productTypeServiceFee) {
            $productQuote = ProductQuote::create(new ProductQuoteCreateDTO($flight, $quote, $userId), $productTypeServiceFee);
            $this->productQuoteRepository->save($productQuote);

            $flightQuote = FlightQuote::create((new FlightQuoteCreateDTO($flight, $productQuote, $quote, $userId)));
            $this->flightQuoteRepository->save($flightQuote);

            $flightQuoteLog = FlightQuoteStatusLog::create($flightQuote->fq_created_user_id, $flightQuote->fq_id, $productQuote->pq_status_id);
            $this->flightQuoteStatusLogRepository->save($flightQuoteLog);

            $this->createQuotePaxPrice($flightQuote, $productQuote, $quote);

            $this->calcProductQuotePrice($productQuote, $flightQuote);

            $this->createFlightTrip($flightQuote, $quote);
        });
    }

    /**
     * @param FlightQuotePaxPrice $flightQuotePaxPrice
     * @param float $markup
     * @throws \Throwable
     */
    public function updateAgentMarkup(FlightQuotePaxPrice $flightQuotePaxPrice, float $markup): void
    {
        $this->transactionManager->wrap(function () use ($flightQuotePaxPrice, $markup) {
            $flightQuotePaxPrice->qpp_agent_mark_up = $markup;
            $this->flightQuotePaxPriceRepository->save($flightQuotePaxPrice);

            $flightQuote = $flightQuotePaxPrice->qppFlightQuote;
            $productQuote = $flightQuote->fqProductQuote;

            $this->calcProductQuotePrice($productQuote, $flightQuote);

            if ($productQuote->pq_order_id) {
                $this->orderPriceUpdater->update($productQuote->pq_order_id);
            }

            $offers = OfferProduct::find()->select(['op_offer_id'])->andWhere(['op_product_quote_id' => $productQuote->pq_id])->column();
            foreach ($offers as $offerId) {
                $this->offerPriceUpdater->update($offerId);
            }
        });
    }

    public function prepareFlightQuoteData(FlightQuoteCreateForm $form): array
    {
        $quote = [
            'key' => FlightQuoteHelper::generateHashQuoteKey(uniqid('quote_', true)),
            'gds' => $form->gds,
            'pcc' => $form->pcc,
            'validatingCarrier' => $form->validatingCarrier,
            'fareType' => $form->fareType,
            'tripType' => $form->tripType,
            'cabin' => $form->cabin,
            'currency' => 'USD',
            'recordLocator' => $form->recordLocator,
            'passengers' => [],
            'pricingInfo' => $form->parsedPricingInfo
        ];
        /** @var $price FlightQuotePaxPriceForm */
        foreach ($form->prices as $price) {
            $quote['passengers'][$price->paxCode] = [
                'codeAs' => $price->paxCode,
                'cnt' => $price->cnt,
                'price' => $price->selling,
                'tax' => $price->taxes,
                'baseFare' => $price->fare,
                'baseTax' => $price->taxes,
                'markup' => $price->markup,
            ];
        }
//      $itinerary = ArrayHelper::toArray($form->itinerary);

        $trips = FlightQuoteHelper::getTripsSegmentsData($form->reservationDump, $form->cabin, (int)$form->tripType);

        $quote['trips'] = $trips;

        return $quote;
    }

    /**
     * @param ProductQuote $productQuote
     * @param FlightQuote $flightQuote
     */
    private function calcProductQuotePrice(ProductQuote $productQuote, FlightQuote $flightQuote): void
    {
        $prices = (new FlightQuotePriceCalculator())->calculate($flightQuote, $productQuote->pq_origin_currency_rate);
        $productQuote->updatePrices(
            $prices['originPrice'],
            $prices['appMarkup'],
            $prices['agentMarkup']
        );
        $this->productQuoteRepository->save($productQuote);

//        $priceData = FlightQuoteHelper::getPricesData($flightQuote);
//
//        $systemPrice = ProductQuoteHelper::calcSystemPrice($priceData->total->selling, $productQuote->pq_origin_currency);
//        $productQuote->setQuotePrice(
//            ProductQuoteHelper::roundPrice((float)$priceData->total->net),
//            $systemPrice,
//            ProductQuoteHelper::roundPrice($systemPrice * $productQuote->pq_client_currency_rate),
//            ProductQuoteHelper::roundPrice((float)$priceData->total->serviceFeeSum)
//        );
//        $productQuote->recalculateProfitAmount();
//
//        $this->productQuoteRepository->save($productQuote);
    }

    /**
     * @param FlightQuote $flightQuote
     * @param ProductQuote $productQuote
     * @param array $quote
     */
    private function createQuotePaxPrice(FlightQuote $flightQuote, ProductQuote $productQuote, array $quote): void
    {
        foreach ($quote['passengers'] as $passengerType => $passenger) {
            $flightQuotePaxPrice = FlightQuotePaxPrice::create((new FlightQuotePaxPriceDTO($flightQuote, $productQuote, $passenger, $passengerType, $quote)));
            $this->flightQuotePaxPriceRepository->save($flightQuotePaxPrice);

            for ($i = 0; $i < $passenger['cnt']; $i++) {
                $flightPax = FlightPax::create(new FlightPaxDTO($flightQuote->fqFlight, $passengerType));
                $this->flightPaxRepository->save($flightPax);
            }
        }
    }

    /**
     * @param FlightQuote $flightQuote
     * @param array $quote
     */
    private function createFlightTrip(FlightQuote $flightQuote, array $quote): void
    {
        foreach ($quote['trips'] as $tripKey => $trip) {
            $tripNr = (int)$tripKey + 1;
            $segmentNr = 1;

            $flightTrip = FlightQuoteTrip::create($flightQuote, $trip['duration']);
            $this->flightQuoteTripRepository->save($flightTrip);

            $this->createSegment($trip, $flightQuote, $flightTrip, $tripNr, $segmentNr);
        }
    }

    /**
     * @param array $trip
     * @param FlightQuote $flightQuote
     * @param FlightQuoteTrip $flightQuoteTrip
     * @param int $tripNr
     * @param int $segmentNr
     */
    private function createSegment(array $trip, FlightQuote $flightQuote, FlightQuoteTrip $flightQuoteTrip, int $tripNr, int $segmentNr): void
    {
        foreach ($trip['segments'] as $segment) {
            $ticketId = FlightQuoteHelper::getTicketId($flightQuote, $tripNr, $segmentNr);

            $flightQuoteSegment = FlightQuoteSegment::create((new FlightQuoteSegmentDTO($flightQuote, $flightQuoteTrip, $segment, $ticketId)));
            $this->flightQuoteSegmentRepository->save($flightQuoteSegment);

            if (!empty($segment['stops'])) {
                $this->createQuoteSegmentStop($flightQuoteSegment, $segment);
            }

            if (!empty($segment['baggage'])) {
                $this->createQuoteSegmentPaxBaggage($flightQuoteSegment, $segment);
            }

            $segmentNr++;
        }
    }

    /**
     * @param FlightQuoteSegment $flightQuoteSegment
     * @param array $segment
     */
    private function createQuoteSegmentStop(FlightQuoteSegment $flightQuoteSegment, array $segment): void
    {
        foreach ($segment['stops'] as $stop) {
            $flightQuoteSegmentStop = FlightQuoteSegmentStop::create((new FlightQuoteSegmentStopDTO($flightQuoteSegment, $stop)));
            $this->flightQuoteSegmentStopRepository->save($flightQuoteSegmentStop);
        }
    }

    /**
     * @param FlightQuoteSegment $flightQuoteSegment
     * @param array $segment
     */
    private function createQuoteSegmentPaxBaggage(FlightQuoteSegment $flightQuoteSegment, array $segment): void
    {
        foreach ($segment['baggage'] as $paxType => $baggage) {
            $flightQuoteSegmentPaxBaggage = FlightQuoteSegmentPaxBaggage::create((new FlightQuoteSegmentPaxBaggageDTO($flightQuoteSegment, $paxType, $baggage)));
            $this->flightQuoteSegmentPaxBaggageRepository->save($flightQuoteSegmentPaxBaggage);

            if (!empty($baggage['charge'])) {
                $this->createQuoteSegmentPaxBaggageCharge($flightQuoteSegment, $paxType, $baggage);
            }
        }
    }

    /**
     * @param FlightQuoteSegment $flightQuoteSegment
     * @param string $paxType
     * @param array $baggage
     */
    private function createQuoteSegmentPaxBaggageCharge(FlightQuoteSegment $flightQuoteSegment, string $paxType, array $baggage): void
    {
        foreach ($baggage['charge'] as $charge) {
            $paxBaggageCharge = FlightQuoteSegmentPaxBaggageCharge::create((new FlightQuoteSegmentPaxBaggageChargeDTO($flightQuoteSegment, $paxType, $charge)));
            $this->baggageChargeRepository->save($paxBaggageCharge);
        }
    }

    /**
     * @param Productable|Flight $flightProduct
     * @param QuotesForm $form
     */
    public function c2bHandle(Productable $flightProduct, QuotesForm $form): void
    {
        try {
            $productTypeServiceFee = null;
            $productType = ProductType::find()->select(['pt_service_fee_percent'])->byFlight()->asArray()->one();
            if ($productType && $productType['pt_service_fee_percent']) {
                $productTypeServiceFee = $productType['pt_service_fee_percent'];
            }

            $quoteData = FlightQuoteSearchHelper::parseQuoteData(Json::decode($form->originSearchData));

            $productQuote = ProductQuote::create(new ProductQuoteCreateDTO($flightProduct, $quoteData, null), $productTypeServiceFee);
            $productQuote->pq_order_id = $form->orderId;
            if ($form->isFailed()) {
                $productQuote->failed();
            } else {
                $productQuote->pq_status_id = ProductQuoteStatus::IN_PROGRESS;
            }
            $this->productQuoteRepository->save($productQuote);

            if (isset($form->options)) {
                foreach ($form->options as $optionsForm) {
                    $productOption = $this->productOptionRepository->findByKey($optionsForm->productOptionKey);

                    $productQuoteOption = ProductQuoteOption::create(
                        $productQuote->pq_id,
                        $productOption->po_id,
                        $optionsForm->name,
                        $optionsForm->description,
                        $optionsForm->price,
                        $optionsForm->price,
                        null,
                        null
                    );
                    $productQuoteOption->calculateClientPrice();
                    $productQuoteOption->pending();
                    $this->productQuoteOptionRepository->save($productQuoteOption);
                }
            }

            $productHolder = ProductHolder::create(
                $productQuote->pq_product_id,
                $form->holder->firstName,
                $form->holder->lastName,
                $form->holder->email,
                $form->holder->phone,
            );
            $this->productHolderRepository->save($productHolder);

            $flightQuote = FlightQuote::create((new FlightQuoteCreateDTO($flightProduct, $productQuote, $quoteData, null)));
            $this->flightQuoteRepository->save($flightQuote);

            $flightQuoteLog = FlightQuoteStatusLog::create($flightQuote->fq_created_user_id, $flightQuote->fq_id, $productQuote->pq_status_id);
            $this->flightQuoteStatusLogRepository->save($flightQuoteLog);

            foreach ($quoteData['passengers'] as $passengerType => $passenger) {
                $flightQuotePaxPrice = FlightQuotePaxPrice::create((new FlightQuotePaxPriceDTO($flightQuote, $productQuote, $passenger, $passengerType, $quoteData)));
                $this->flightQuotePaxPriceRepository->save($flightQuotePaxPrice);

                $paxKeySet = null;
                for ($i = 0; $i < $passenger['cnt']; $i++) {
                    $flightPax = FlightPax::create(new FlightPaxDTO($flightQuote->fqFlight, $passengerType));
                    foreach ($form->flightPaxData as $key => $paxForm) {
                        if ($paxForm->type === $passengerType && $paxKeySet !== $key) {
                            $flightPax->fp_first_name = $paxForm->first_name;
                            $flightPax->fp_last_name = $paxForm->last_name;
                            $flightPax->fp_middle_name = $paxForm->middle_name;
                            $flightPax->fp_dob = $paxForm->birth_date;
                            $flightPax->fp_nationality = $paxForm->nationality;
                            $flightPax->fp_gender = $paxForm->gender;
                            $flightPax->fp_email = $paxForm->email;
                            $flightPax->fp_citizenship = $paxForm->citizenship;

                            $paxKeySet = $key;
                            break;
                        }
                    }
                    $this->flightPaxRepository->save($flightPax);
                }
            }

            $this->calcProductQuotePrice($productQuote, $flightQuote);

            $this->createFlightTrip($flightQuote, $quoteData);
        } catch (\Throwable $e) {
            $dto = new OrderC2BDtoException(
                $flightProduct,
                $form->quoteOtaId
            );
            throw new OrderC2BException($dto, $e->getMessage(), FlightCodeException::API_C2B_HANDLE);
        }
    }
}
