<?php

namespace webapi\src\services\flight;

use frontend\helpers\JsonHelper;
use modules\flight\models\FlightPax;
use modules\flight\models\FlightQuote;
use modules\flight\models\FlightQuoteBooking;
use modules\flight\models\FlightQuoteBookingAirline;
use modules\flight\models\FlightQuoteFlight;
use modules\flight\models\FlightQuotePaxPrice;
use modules\flight\models\FlightQuoteSegment;
use modules\flight\models\FlightQuoteSegmentPaxBaggage;
use modules\flight\models\FlightQuoteTicket;
use modules\flight\models\FlightQuoteTrip;
use modules\flight\src\dto\flightQuotePaxPrice\FlightQuotePaxPriceApiBoDto;
use modules\flight\src\dto\flightSegment\FlightQuoteSegmentApiBoDto;
use modules\flight\src\entities\flightQuoteOption\FlightQuoteOption;
use modules\flight\src\repositories\flightPaxRepository\FlightPaxRepository;
use modules\flight\src\repositories\flightQuoteBookingAirline\FlightQuoteBookingAirlineRepository;
use modules\flight\src\repositories\flightQuoteFlight\FlightQuoteFlightRepository;
use modules\flight\src\repositories\flightQuoteBooking\FlightQuoteBookingRepository;
use modules\flight\src\repositories\flightQuoteOption\FlightQuoteOptionRepository;
use modules\flight\src\repositories\flightQuotePaxPriceRepository\FlightQuotePaxPriceRepository;
use modules\flight\src\repositories\flightQuoteRepository\FlightQuoteRepository;
use modules\flight\src\repositories\flightQuoteSegment\FlightQuoteSegmentRepository;
use modules\flight\src\repositories\flightQuoteSegmentPaxBaggageRepository\FlightQuoteSegmentPaxBaggageRepository;
use modules\flight\src\repositories\flightQuoteTicket\FlightQuoteTicketRepository;
use modules\flight\src\repositories\flightQuoteTripRepository\FlightQuoteTripRepository;
use modules\flight\src\services\flightQuote\FlightQuotePriceCalculator;
use modules\order\src\entities\order\OrderRepository;
use modules\order\src\entities\orderRefund\OrderRefund;
use modules\order\src\entities\orderRefund\OrderRefundRepository;
use modules\order\src\entities\orderTips\OrderTips;
use modules\order\src\entities\orderTips\OrderTipsRepository;
use modules\order\src\services\OrderPriceUpdater;
use modules\product\src\entities\product\Product;
use modules\product\src\entities\productOption\ProductOption;
use modules\product\src\entities\productOption\ProductOptionRepository;
use modules\product\src\entities\productQuote\ProductQuote;
use modules\product\src\entities\productQuote\ProductQuoteQuery;
use modules\product\src\entities\productQuote\ProductQuoteStatus;
use modules\product\src\entities\productQuoteChange\ProductQuoteChangeRepository;
use modules\product\src\entities\productQuoteOption\ProductQuoteOption;
use modules\product\src\entities\productQuoteOption\ProductQuoteOptionRepository;
use modules\product\src\entities\productQuoteOption\ProductQuoteOptionStatus;
use modules\product\src\entities\productQuoteRefund\ProductQuoteRefund;
use modules\product\src\entities\productQuoteRefund\ProductQuoteRefundRepository;
use modules\product\src\entities\productQuoteRelation\ProductQuoteRelation;
use modules\product\src\entities\productType\ProductType;
use modules\product\src\services\productQuote\ProductQuoteReplaceService;
use src\helpers\app\AppHelper;
use src\interfaces\BoWebhookService;
use src\model\caseOrder\entity\CaseOrder;
use src\model\caseOrder\entity\CaseOrderQuery;
use src\repositories\cases\CasesRepository;
use src\repositories\product\ProductQuoteRepository;
use src\services\CurrencyHelper;
use webapi\src\forms\boWebhook\FlightRefundUpdateForm;
use webapi\src\forms\flight\FlightRequestApiForm;
use webapi\src\forms\flight\flights\trips\SegmentApiForm;
use yii\base\Model;
use yii\db\Transaction;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;

/**
 * Class FlightManageApiService
 *
 * @property bool processedStatus
 *
 * @property FlightQuoteFlightRepository $flightQuoteFlightRepository
 * @property FlightQuoteBookingRepository $flightQuoteBookingRepository
 * @property FlightQuoteBookingAirlineRepository $flightQuoteBookingAirlineRepository
 * @property FlightPaxRepository $flightPaxRepository
 * @property FlightQuoteTicketRepository $flightQuoteTicketRepository
 * @property FlightQuotePaxPriceRepository $flightQuotePaxPriceRepository
 * @property FlightQuoteTripRepository $flightQuoteTripRepository
 * @property FlightQuoteSegmentRepository $flightQuoteSegmentRepository
 * @property OrderTipsRepository $orderTipsRepository
 * @property FlightQuoteSegmentPaxBaggageRepository $flightQuoteSegmentPaxBaggageRepository
 * @property FlightQuoteRepository $flightQuoteRepository
 * @property ProductQuoteReplaceService $productQuoteReplaceService
 * @property ProductQuoteRepository $productQuoteRepository
 * @property ProductOptionRepository $productOptionRepository
 * @property ProductQuoteOptionRepository $productQuoteOptionRepository
 * @property OrderRepository $orderRepository
 * @property OrderPriceUpdater $orderPriceUpdater
 * @property FlightQuoteOptionRepository $flightQuoteOptionRepository
 * @property OrderRefundRepository $orderRefundRepository
 * @property ProductQuoteRefundRepository $productQuoteRefundRepository
 * @property CasesRepository $casesRepository
 * @property ProductQuoteChangeRepository $productQuoteChangeRepository
 */
class FlightManageApiService implements BoWebhookService
{
    private bool $processedStatus = false;

    private FlightQuoteFlightRepository $flightQuoteFlightRepository;
    private FlightQuoteBookingRepository $flightQuoteBookingRepository;
    private FlightQuoteBookingAirlineRepository $flightQuoteBookingAirlineRepository;
    private FlightPaxRepository $flightPaxRepository;
    private FlightQuoteTicketRepository $flightQuoteTicketRepository;
    private FlightQuotePaxPriceRepository $flightQuotePaxPriceRepository;
    private FlightQuoteTripRepository $flightQuoteTripRepository;
    private FlightQuoteSegmentRepository $flightQuoteSegmentRepository;
    private OrderTipsRepository $orderTipsRepository;
    private FlightQuoteSegmentPaxBaggageRepository $flightQuoteSegmentPaxBaggageRepository;
    private FlightQuoteRepository $flightQuoteRepository;
    private ProductQuoteReplaceService $productQuoteReplaceService;
    private ProductQuoteRepository $productQuoteRepository;
    private ProductOptionRepository $productOptionRepository;
    private ProductQuoteOptionRepository $productQuoteOptionRepository;
    private OrderRepository $orderRepository;
    private OrderPriceUpdater $orderPriceUpdater;
    private FlightQuoteOptionRepository $flightQuoteOptionRepository;
    private ProductQuoteRefundRepository $productQuoteRefundRepository;
    private CasesRepository $casesRepository;
    private ProductQuoteChangeRepository $productQuoteChangeRepository;

    /**
     * @param FlightQuoteFlightRepository $flightQuoteFlightRepository
     * @param FlightQuoteBookingRepository $flightQuoteBookingRepository
     * @param FlightQuoteBookingAirlineRepository $flightQuoteBookingAirlineRepository
     * @param FlightPaxRepository $flightPaxRepository
     * @param FlightQuoteTicketRepository $flightQuoteTicketRepository
     * @param FlightQuotePaxPriceRepository $flightQuotePaxPriceRepository
     * @param FlightQuoteTripRepository $flightQuoteTripRepository
     * @param FlightQuoteSegmentRepository $flightQuoteSegmentRepository
     * @param OrderTipsRepository $orderTipsRepository
     * @param FlightQuoteSegmentPaxBaggageRepository $flightQuoteSegmentPaxBaggageRepository
     * @param FlightQuoteRepository $flightQuoteRepository
     * @param ProductQuoteReplaceService $productQuoteReplaceService
     * @param ProductQuoteRepository $productQuoteRepository
     * @param ProductOptionRepository $productOptionRepository
     * @param ProductQuoteOptionRepository $productQuoteOptionRepository
     * @param OrderRepository $orderRepository
     * @param OrderPriceUpdater $orderPriceUpdater
     * @param FlightQuoteOptionRepository $flightQuoteOptionRepository
     * @param OrderRefundRepository $orderRefundRepository
     * @param ProductQuoteRefundRepository $productQuoteRefundRepository
     * @param CasesRepository $casesRepository
     * @param ProductQuoteChangeRepository $productQuoteChangeRepository
     */
    public function __construct(
        FlightQuoteFlightRepository $flightQuoteFlightRepository,
        FlightQuoteBookingRepository $flightQuoteBookingRepository,
        FlightQuoteBookingAirlineRepository $flightQuoteBookingAirlineRepository,
        FlightPaxRepository $flightPaxRepository,
        FlightQuoteTicketRepository $flightQuoteTicketRepository,
        FlightQuotePaxPriceRepository $flightQuotePaxPriceRepository,
        FlightQuoteTripRepository $flightQuoteTripRepository,
        FlightQuoteSegmentRepository $flightQuoteSegmentRepository,
        OrderTipsRepository $orderTipsRepository,
        FlightQuoteSegmentPaxBaggageRepository $flightQuoteSegmentPaxBaggageRepository,
        FlightQuoteRepository $flightQuoteRepository,
        ProductQuoteReplaceService $productQuoteReplaceService,
        ProductQuoteRepository $productQuoteRepository,
        ProductOptionRepository $productOptionRepository,
        ProductQuoteOptionRepository $productQuoteOptionRepository,
        OrderRepository $orderRepository,
        OrderPriceUpdater $orderPriceUpdater,
        FlightQuoteOptionRepository $flightQuoteOptionRepository,
        OrderRefundRepository $orderRefundRepository,
        ProductQuoteRefundRepository $productQuoteRefundRepository,
        CasesRepository $casesRepository,
        ProductQuoteChangeRepository $productQuoteChangeRepository
    ) {
        $this->flightQuoteFlightRepository = $flightQuoteFlightRepository;
        $this->flightQuoteBookingRepository = $flightQuoteBookingRepository;
        $this->flightQuoteBookingAirlineRepository = $flightQuoteBookingAirlineRepository;
        $this->flightPaxRepository = $flightPaxRepository;
        $this->flightQuoteTicketRepository = $flightQuoteTicketRepository;
        $this->flightQuotePaxPriceRepository = $flightQuotePaxPriceRepository;
        $this->flightQuoteTripRepository = $flightQuoteTripRepository;
        $this->flightQuoteSegmentRepository = $flightQuoteSegmentRepository;
        $this->orderTipsRepository = $orderTipsRepository;
        $this->flightQuoteSegmentPaxBaggageRepository = $flightQuoteSegmentPaxBaggageRepository;
        $this->flightQuoteRepository = $flightQuoteRepository;
        $this->productQuoteReplaceService = $productQuoteReplaceService;
        $this->productQuoteRepository = $productQuoteRepository;
        $this->productOptionRepository = $productOptionRepository;
        $this->productQuoteOptionRepository = $productQuoteOptionRepository;
        $this->orderRepository = $orderRepository;
        $this->orderPriceUpdater = $orderPriceUpdater;
        $this->flightQuoteOptionRepository = $flightQuoteOptionRepository;
        $this->orderRefundRepository = $orderRefundRepository;
        $this->productQuoteRefundRepository = $productQuoteRefundRepository;
        $this->casesRepository = $casesRepository;
        $this->productQuoteChangeRepository = $productQuoteChangeRepository;
    }

    public function ticketIssue(FlightRequestApiForm $flightRequestApiForm): void
    {
        $flightPaxProcessed = [];
        $this->flightPaxRepository->removePaxByFlight($flightRequestApiForm->flightQuote->fq_flight_id);

        foreach ($flightRequestApiForm->getFlightApiForms() as $key => $flightApiForm) {
            $flightQuoteFlight = FlightQuoteFlight::create(
                $flightRequestApiForm->flightQuote->getId(),
                self::mapTripType($flightApiForm->flightType),
                $flightApiForm->validatingCarrier,
                $flightApiForm->uniqueId,
                $flightApiForm->status,
                $flightApiForm->pnr,
                $flightApiForm->validatingCarrier,
                $flightApiForm->getOriginalDataJson()
            );
            $flightQuoteFlightId = $this->flightQuoteFlightRepository->save($flightQuoteFlight);

            foreach ($flightApiForm->getBookingInfoForms() as $bookingInfoApiForm) {
                if (!$bookingInfoApiForm->isIssued()) {
                    continue;
                }
                $flightQuoteBooking = FlightQuoteBooking::create(
                    $flightQuoteFlightId,
                    $bookingInfoApiForm->bookingId,
                    $bookingInfoApiForm->pnr,
                    $bookingInfoApiForm->gds,
                    null,
                    $bookingInfoApiForm->validatingCarrier
                );
                $flightQuoteBookingId = $this->flightQuoteBookingRepository->save($flightQuoteBooking);

                foreach ($bookingInfoApiForm->getAirlinesCodeForms() as $airlinesCodeApiForm) {
                    $flightQuoteBookingAirline = FlightQuoteBookingAirline::create(
                        $flightQuoteBookingId,
                        $airlinesCodeApiForm->recordLocator,
                        $airlinesCodeApiForm->code
                    );
                    $this->flightQuoteBookingAirlineRepository->save($flightQuoteBookingAirline);
                }

                foreach ($bookingInfoApiForm->getPassengerForms() as $passengerApiForm) {
                    if (ArrayHelper::keyExists($passengerApiForm->getHashIdentity(), $flightPaxProcessed)) {
                        $flightPax = $flightPaxProcessed[$passengerApiForm->getHashIdentity()];
                    } else {
                        $flightPax = FlightPax::createByParams(
                            $flightRequestApiForm->flightQuote->fq_flight_id,
                            $passengerApiForm->paxType,
                            $passengerApiForm->first_name,
                            $passengerApiForm->last_name,
                            $passengerApiForm->middle_name,
                            $passengerApiForm->birth_date,
                            $passengerApiForm->gender,
                            $passengerApiForm->nationality
                        );
                        $this->flightPaxRepository->save($flightPax);
                        $flightPaxProcessed[$passengerApiForm->getHashIdentity()] = $flightPax;
                    }
                    $flightQuoteTicket = FlightQuoteTicket::create($flightPax->fp_id, $flightQuoteBookingId, $passengerApiForm->tktNumber);
                    $this->flightQuoteTicketRepository->save($flightQuoteTicket);
                }
            }
        }

        $productQuote = $flightRequestApiForm->flightQuote->fqProductQuote;
        $productQuote->booked();
        $this->productQuoteRepository->save($productQuote);
    }

    public function replaceProcessing(FlightRequestApiForm $flightRequestApiForm, FlightQuote $newFlightQuote): FlightManageApiService
    {
        $flightPaxProcessed = [];
        $this->orderTipsRepository->removeByOrderId($flightRequestApiForm->order->getId());

        foreach ($flightRequestApiForm->getFlightApiForms() as $flightApiForm) {
            if (!$flightApiForm->isIssued()) {
                continue;
            }

            $flightQuoteFlight = FlightQuoteFlight::create(
                $newFlightQuote->getId(),
                self::mapTripType($flightApiForm->flightType),
                $flightApiForm->validatingCarrier,
                $flightApiForm->uniqueId,
                $flightApiForm->status,
                $flightApiForm->pnr,
                $flightApiForm->validatingCarrier,
                $flightApiForm->getOriginalDataJson()
            );
            $flightQuoteFlightId = $this->flightQuoteFlightRepository->save($flightQuoteFlight);

            foreach ($flightApiForm->getBookingInfoForms() as $bookingInfoApiForm) {
                if (!$bookingInfoApiForm->isIssued()) {
                    continue;
                }
                $flightQuoteBooking = FlightQuoteBooking::create(
                    $flightQuoteFlightId,
                    $bookingInfoApiForm->bookingId,
                    $bookingInfoApiForm->pnr,
                    $bookingInfoApiForm->gds,
                    null,
                    $bookingInfoApiForm->validatingCarrier
                );
                $flightQuoteBookingId = $this->flightQuoteBookingRepository->save($flightQuoteBooking);

                if (($insuranceApiForm = $bookingInfoApiForm->getInsuranceApiForm()) && $insuranceApiForm->paid) {
                    $productOption = $this->productOptionRepository->findByKey(ProductOption::TRAVEL_GUARD_FLIGHT_KEY);

                    $productQuoteOption = ProductQuoteOption::create(
                        $newFlightQuote->fq_product_quote_id,
                        $productOption->po_id,
                        ProductOption::TRAVEL_GUARD_FLIGHT_KEY,
                        'PolicyNumber: ' . $insuranceApiForm->policyNumber,
                        $insuranceApiForm->amount,
                        $insuranceApiForm->amount,
                        null,
                        JsonHelper::encode($insuranceApiForm->toArray())
                    );
                    $productQuoteOption->done();
                    $this->productQuoteOptionRepository->save($productQuoteOption);
                }

                foreach ($bookingInfoApiForm->getAirlinesCodeForms() as $airlinesCodeApiForm) {
                    $flightQuoteBookingAirline = FlightQuoteBookingAirline::create(
                        $flightQuoteBookingId,
                        $airlinesCodeApiForm->recordLocator,
                        $airlinesCodeApiForm->code
                    );
                    $this->flightQuoteBookingAirlineRepository->save($flightQuoteBookingAirline);
                }

                foreach ($bookingInfoApiForm->getPassengerForms() as $passengerApiForm) {
                    if (ArrayHelper::keyExists($passengerApiForm->getHashIdentity(), $flightPaxProcessed)) {
                        $flightPax = $flightPaxProcessed[$passengerApiForm->getHashIdentity()];
                    } else {
                        $flightPax = FlightPax::createByParams(
                            $newFlightQuote->fq_flight_id,
                            $passengerApiForm->paxType,
                            $passengerApiForm->first_name,
                            $passengerApiForm->last_name,
                            $passengerApiForm->middle_name,
                            $passengerApiForm->birth_date,
                            $passengerApiForm->gender,
                            $passengerApiForm->nationality
                        );
                        $this->flightPaxRepository->save($flightPax);
                        $flightPaxProcessed[$passengerApiForm->getHashIdentity()] = $flightPax;
                    }
                    if ($passengerApiForm->tktNumber) {
                        $flightQuoteTicket = FlightQuoteTicket::create($flightPax->fp_id, $flightQuoteBookingId, $passengerApiForm->tktNumber);
                        $this->flightQuoteTicketRepository->save($flightQuoteTicket);
                    }
                }
            }

            if ($priceApiForm = $flightApiForm->getPriceApiForm()) {
                foreach ($priceApiForm->getPriceDetailApiForms() as $priceDetailApiForm) {
                    $flightQuotePaxPriceApiBoDto = new FlightQuotePaxPriceApiBoDto(
                        $newFlightQuote,
                        $newFlightQuote->fqProductQuote,
                        $priceDetailApiForm
                    );
                    $flightQuotePaxPrice = FlightQuotePaxPrice::createFromBo($flightQuotePaxPriceApiBoDto);
                    $this->flightQuotePaxPriceRepository->save($flightQuotePaxPrice);
                }

                if ($priceApiForm->tips > 0) {
                    $orderTips = new OrderTips();
                    $orderTips->ot_order_id = $flightRequestApiForm->order->getId();
                    $orderTips->ot_client_amount = $priceApiForm->tips;
                    $orderTips->ot_amount = $priceApiForm->tips;
                    $this->orderTipsRepository->save($orderTips);
                }
            }

            foreach ($flightApiForm->getTripSegments() as $tripKey => $tripSegments) {
                $flightTrip = FlightQuoteTrip::create($newFlightQuote, 0);
                $this->flightQuoteTripRepository->save($flightTrip);

                foreach ($tripSegments as $segmentApiForm) {
                    /** @var SegmentApiForm $segmentApiForm */
                    $flightQuoteSegmentApiBoDto = new FlightQuoteSegmentApiBoDto(
                        $newFlightQuote->getId(),
                        $flightTrip->fqt_id,
                        $segmentApiForm
                    );
                    $flightQuoteSegment = FlightQuoteSegment::createFromBo($flightQuoteSegmentApiBoDto);
                    $this->flightQuoteSegmentRepository->save($flightQuoteSegment);

                    $flightQuoteSegmentPaxBaggage = FlightQuoteSegmentPaxBaggage::createByParams(
                        FlightPax::getPaxId(FlightPax::PAX_ADULT),
                        $flightQuoteSegment->fqs_id,
                        $segmentApiForm->carryOn,
                        $segmentApiForm->airline,
                        $segmentApiForm->baggage
                    );
                    $this->flightQuoteSegmentPaxBaggageRepository->save($flightQuoteSegmentPaxBaggage);

                    $flightTrip->fqt_duration += $flightQuoteSegment->fqs_duration;
                }
                $this->flightQuoteTripRepository->save($flightTrip);
            }
        }

        if ($flightRequestApiForm->optionApiForms) {
            foreach ($flightRequestApiForm->optionApiForms as $key => $optionApiForm) {
                $productQuoteOption = ProductQuoteOption::create(
                    $newFlightQuote->fq_product_quote_id,
                    $optionApiForm->getProductOption()->po_id,
                    $optionApiForm->pqo_name,
                    $optionApiForm->pqo_description,
                    $optionApiForm->pqo_price,
                    $optionApiForm->pqo_price + $optionApiForm->pqo_markup,
                    $optionApiForm->pqo_markup,
                    JsonHelper::encode($optionApiForm->pqo_request_data)
                );
                $productQuoteOption->done();
                $this->productQuoteOptionRepository->save($productQuoteOption);

                $flightQuoteOption = FlightQuoteOption::create(
                    $productQuoteOption->pqo_id,
                    null,
                    null,
                    null,
                    $optionApiForm->pqo_name,
                    $optionApiForm->pqo_markup,
                    $optionApiForm->pqo_markup,
                    $optionApiForm->pqo_price,
                    $optionApiForm->pqo_price,
                    $optionApiForm->pqo_price,
                    $optionApiForm->pqo_price,
                    null
                );
                $this->flightQuoteOptionRepository->save($flightQuoteOption);
            }
        }

        return $this;
    }

    public function recalculatePricingProcessing(ProductQuote $productQuote, FlightQuote $flightQuote): FlightManageApiService
    {
        $amountFare = $amountTax = $totalPaxPrice = 0.00;
        foreach ($flightQuote->flightQuotePaxPrices as $paxPrice) {
            $amountFare += $paxPrice->qpp_fare * $paxPrice->qpp_cnt;
            $amountTax += $paxPrice->qpp_tax * $paxPrice->qpp_cnt;
        }
        $totalPaxPrice = $amountFare + $amountTax;

        $productQuote->updatePricesC2b($totalPaxPrice);
        $this->productQuoteRepository->save($productQuote);

        $this->orderPriceUpdater->updateC2b($productQuote->pqOrder);

        return $this;
    }

    public function checkProcessedStatus(FlightRequestApiForm $flightRequestApiForm): bool
    {
        foreach ($flightRequestApiForm->getFlightApiForms() as $flightApiForm) {
            if ($flightApiForm->isIssued()) {
                $this->setProcessedStatus(true);
                break;
            }
        }
        return $this->isProcessedStatus();
    }

    public static function getFlightQuoteByOrderId(int $orderId): ?FlightQuote
    {
        $flightQuote = FlightQuote::find()
            ->innerJoin(ProductQuote::tableName(), 'pq_id = fq_product_quote_id')
            ->innerJoin(Product::tableName(), 'pr_id = pq_product_id')
            ->andWhere(['pq_order_id' => $orderId])
            ->andWhere(['pr_type_id' => ProductType::PRODUCT_FLIGHT])
            ->andWhere(['NOT', ['pq_status_id' => ProductQuoteStatus::CANCEL_GROUP]])
            ->orderBy(['fq_id' => SORT_DESC])
            ->one();
        /** @var FlightQuote|null $flightQuote */
        return $flightQuote;
    }

    /**
     * @param string $tripType
     * @return false|int|string
     */
    private static function mapTripType(string $tripType)
    {
        return array_search($tripType, FlightQuoteFlight::TRIP_TYPE_LIST);
    }

    public function isProcessedStatus(): bool
    {
        return $this->processedStatus;
    }

    /**
     * @param bool $processedStatus
     */
    public function setProcessedStatus(bool $processedStatus): void
    {
        $this->processedStatus = $processedStatus;
    }

    /**
     * @param FlightRefundUpdateForm $form
     */
    public function processRequest(Model $form): void
    {
        $productQuote = ProductQuoteQuery::getProductQuoteByBookingId($form->booking_id);
        $transaction = new Transaction(['db' => \Yii::$app->db]);
        if ($productQuote) {
            $productQuoteRefund = $productQuote->productQuoteLastRefund;
            if ($productQuoteRefund && $productQuoteRefund->isInProcessing()) {
                $orderRefund = $productQuoteRefund->orderRefund;
                $case = $productQuoteRefund->case;

                try {
                    $transaction->begin();
                    if ($orderRefund) {
                        $orderRefund->done();
                        $this->orderRefundRepository->save($orderRefund);
                    }
                    $productQuoteRefund->complete();
                    $this->productQuoteRefundRepository->save($productQuoteRefund);
                    if ($case) {
                        $case->solved(null, 'Refund request approved');
                        $this->casesRepository->save($case);
                    }
                    $transaction->commit();
                } catch (\Throwable $e) {
                    $transaction->rollBack();
                }
                return;
            }

            $query = ProductQuoteRelation::find()
                ->byParentQuoteId($productQuote->pq_id)
                ->reprotection();

            $reprotectionQuoteInProgress = $query
                ->innerJoin(ProductQuote::tableName(), 'pq_id = pqr_related_pq_id and pq_status_id = :pqStatusId', [
                    'pqStatusId' => ProductQuoteStatus::IN_PROGRESS
                ])
                ->one();

            if ($reprotectionQuoteInProgress) {
                $reprotectionQuotes = ProductQuoteQuery::getReprotectionQuotesByOriginQuote($productQuote->pq_id);
                $productQuoteChange = $productQuote->productQuoteLastChange;
                $case = null;
                if ($productQuoteChange) {
                    $case = $productQuoteChange->pqcCase;
                }

                $orderRefund = OrderRefund::create(
                    OrderRefund::generateUid(),
                    $productQuote->pq_order_id,
                    $productQuote->pqOrder->or_app_total,
                    $productQuote->pqOrder->or_client_currency,
                    $productQuote->pqOrder->or_client_currency_rate,
                    $productQuote->pqOrder->or_client_total,
                    $case->cs_id ?? null
                );
                $orderRefund->done();
                $orderRefund->clientDone();
                $orderRefund->detachBehavior('user');

                $productQuoteRefund = ProductQuoteRefund::create(
                    null,
                    $productQuote->pq_id,
                    $productQuote->pq_price,
                    $productQuote->pqOrder->or_client_currency,
                    $productQuote->pqOrder->or_client_currency_rate,
                    $case->cs_id ?? null
                );
                $productQuoteRefund->complete();
                $productQuoteRefund->detachBehavior('user');

                try {
                    $transaction->begin();


                    foreach ($reprotectionQuotes as $reprotectionQuote) {
                        $reprotectionQuote->cancelled();
                        $this->productQuoteRepository->save($reprotectionQuote);
                    }

                    if ($productQuoteChange) {
                        $productQuoteChange->declined();
                        $this->productQuoteChangeRepository->save($productQuoteChange);
                    }

                    if (!$case && $caseOrderRelation = CaseOrderQuery::getRelationByOrderId($productQuote->pq_order_id)) {
                        $case = $caseOrderRelation->cases;
                    }

                    $this->orderRefundRepository->save($orderRefund);
                    $productQuoteRefund->pqr_order_refund_id = $orderRefund->orr_id;
                    $this->productQuoteRefundRepository->save($productQuoteRefund);

                    if ($case) {
                        $case->solved(null, 'Refund request approved');
                        $this->casesRepository->save($case);
                    }

                    $transaction->commit();
                } catch (\Throwable $e) {
                    $transaction->rollBack();
                    if ($case) {
                        $case->error(null, 'Create refund error.');
                        $this->casesRepository->save($case);
                    }
                }
            }
        }
    }
}
