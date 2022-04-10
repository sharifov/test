<?php

namespace modules\flight\src\useCases\sale;

use common\components\SearchService;
use common\models\Currency;
use frontend\helpers\JsonHelper;
use modules\flight\models\Flight;
use modules\flight\models\FlightPax;
use modules\flight\models\FlightQuote;
use modules\flight\models\FlightQuoteBooking;
use modules\flight\models\FlightQuoteFlight;
use modules\flight\models\FlightQuotePaxPrice;
use modules\flight\models\FlightQuoteSegment;
use modules\flight\models\FlightQuoteSegmentPaxBaggage;
use modules\flight\models\FlightQuoteTicket;
use modules\flight\models\FlightQuoteTrip;
use modules\flight\src\dto\flightSegment\FlightQuoteSegmentApiBoDto;
use modules\flight\src\entities\flightQuoteOption\FlightQuoteOption;
use modules\flight\src\repositories\flight\FlightRepository;
use modules\flight\src\repositories\flightPaxRepository\FlightPaxRepository;
use modules\flight\src\repositories\flightQuoteBooking\FlightQuoteBookingRepository;
use modules\flight\src\repositories\flightQuoteFlight\FlightQuoteFlightRepository;
use modules\flight\src\repositories\flightQuoteOption\FlightQuoteOptionRepository;
use modules\flight\src\repositories\flightQuotePaxPriceRepository\FlightQuotePaxPriceRepository;
use modules\flight\src\repositories\flightQuoteRepository\FlightQuoteRepository;
use modules\flight\src\repositories\flightQuoteSegment\FlightQuoteSegmentRepository;
use modules\flight\src\repositories\flightQuoteSegmentPaxBaggageRepository\FlightQuoteSegmentPaxBaggageRepository;
use modules\flight\src\repositories\flightQuoteTicket\FlightQuoteTicketRepository;
use modules\flight\src\repositories\flightQuoteTripRepository\FlightQuoteTripRepository;
use modules\flight\src\useCases\flightQuote\create\FlightQuoteCreateDTO;
use modules\flight\src\useCases\flightQuote\create\FlightQuoteSegmentDTO;
use modules\flight\src\useCases\flightQuote\create\ProductQuoteCreateDTO;
use modules\flight\src\useCases\flightQuote\FlightQuoteManageService;
use modules\flight\src\useCases\sale\dto\ProductQuoteCreateFromSaleDto;
use modules\flight\src\useCases\sale\form\FlightPaxForm;
use modules\flight\src\useCases\sale\form\PriceQuotesForm;
use modules\order\src\entities\order\Order;
use modules\order\src\payment\PaymentRepository;
use modules\order\src\services\createFromSale\OrderCreateFromSaleForm;
use modules\order\src\services\OrderPriceUpdater;
use modules\product\src\entities\product\dto\CreateDto;
use modules\product\src\entities\product\Product;
use modules\product\src\entities\product\ProductRepository;
use modules\product\src\entities\productQuote\ProductQuote;
use modules\product\src\entities\productQuote\ProductQuoteStatus;
use modules\product\src\entities\productQuoteOption\ProductQuoteOption;
use modules\product\src\entities\productQuoteOption\ProductQuoteOptionRepository;
use modules\product\src\entities\productType\ProductType;
use modules\product\src\useCases\product\create\ProductCreateService;
use src\helpers\app\AppHelper;
use src\helpers\ErrorsToStringHelper;
use src\repositories\product\ProductQuoteRepository;
use src\services\CurrencyHelper;
use webapi\src\forms\flight\flights\trips\SegmentApiForm;
use webapi\src\forms\flight\options\OptionApiForm;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * Class FlightFromSaleService
 *
 * @property PaymentRepository $paymentRepository
 * @property ProductCreateService $productCreateService
 * @property ProductQuoteRepository $productQuoteRepository
 * @property FlightQuoteRepository $flightQuoteRepository
 * @property FlightQuoteTripRepository $flightQuoteTripRepository
 * @property FlightQuoteSegmentRepository $flightQuoteSegmentRepository
 * @property ProductRepository $productRepository
 * @property FlightPaxRepository $flightPaxRepository
 * @property FlightQuotePaxPriceRepository $flightQuotePaxPriceRepository
 * @property FlightRepository $flightRepository
 * @property FlightQuoteFlightRepository $flightQuoteFlightRepository
 * @property FlightQuoteBookingRepository $flightQuoteBookingRepository
 * @property FlightQuoteTicketRepository $flightQuoteTicketRepository
 * @property FlightQuoteManageService $flightQuoteManageService
 * @property FlightQuoteSegmentPaxBaggageRepository $flightQuoteSegmentPaxBaggageRepository
 * @property ProductQuoteOptionRepository $productQuoteOptionRepository
 * @property FlightQuoteOptionRepository $flightQuoteOptionRepository
 * @property OrderPriceUpdater $orderPriceUpdater
 *
 * @property array $options
 */
class FlightFromSaleService
{
    private PaymentRepository $paymentRepository;
    private ProductCreateService $productCreateService;
    private ProductQuoteRepository $productQuoteRepository;
    private FlightQuoteRepository $flightQuoteRepository;
    private FlightQuoteTripRepository $flightQuoteTripRepository;
    private FlightQuoteSegmentRepository $flightQuoteSegmentRepository;
    private ProductRepository $productRepository;
    private FlightPaxRepository $flightPaxRepository;
    private FlightQuotePaxPriceRepository $flightQuotePaxPriceRepository;
    private FlightRepository $flightRepository;
    private FlightQuoteFlightRepository $flightQuoteFlightRepository;
    private FlightQuoteBookingRepository $flightQuoteBookingRepository;
    private FlightQuoteTicketRepository $flightQuoteTicketRepository;
    private FlightQuoteManageService $flightQuoteManageService;
    private FlightQuoteSegmentPaxBaggageRepository $flightQuoteSegmentPaxBaggageRepository;
    private ProductQuoteOptionRepository $productQuoteOptionRepository;
    private FlightQuoteOptionRepository $flightQuoteOptionRepository;
    private OrderPriceUpdater $orderPriceUpdater;

    private array $options = [
        'package', 'cfar', 'flexibleTicket', 'autoCheckIn', 'pdp', 'travelInsurance'
    ];

    /**
     * @param PaymentRepository $paymentRepository
     * @param ProductCreateService $productCreateService
     * @param ProductQuoteRepository $productQuoteRepository
     * @param FlightQuoteRepository $flightQuoteRepository
     * @param FlightQuoteTripRepository $flightQuoteTripRepository
     * @param FlightQuoteSegmentRepository $flightQuoteSegmentRepository
     * @param ProductRepository $productRepository
     * @param FlightPaxRepository $flightPaxRepository
     * @param FlightQuotePaxPriceRepository $flightQuotePaxPriceRepository
     * @param FlightRepository $flightRepository
     * @param FlightQuoteFlightRepository $flightQuoteFlightRepository
     * @param FlightQuoteBookingRepository $flightQuoteBookingRepository
     * @param FlightQuoteTicketRepository $flightQuoteTicketRepository
     * @param FlightQuoteManageService $flightQuoteManageService
     * @param FlightQuoteSegmentPaxBaggageRepository $flightQuoteSegmentPaxBaggageRepository
     * @param ProductQuoteOptionRepository $productQuoteOptionRepository
     * @param FlightQuoteOptionRepository $flightQuoteOptionRepository
     * @param OrderPriceUpdater $orderPriceUpdater
     */
    public function __construct(
        PaymentRepository $paymentRepository,
        ProductCreateService $productCreateService,
        ProductQuoteRepository $productQuoteRepository,
        FlightQuoteRepository $flightQuoteRepository,
        FlightQuoteTripRepository $flightQuoteTripRepository,
        FlightQuoteSegmentRepository $flightQuoteSegmentRepository,
        ProductRepository $productRepository,
        FlightPaxRepository $flightPaxRepository,
        FlightQuotePaxPriceRepository $flightQuotePaxPriceRepository,
        FlightRepository $flightRepository,
        FlightQuoteFlightRepository $flightQuoteFlightRepository,
        FlightQuoteBookingRepository $flightQuoteBookingRepository,
        FlightQuoteTicketRepository $flightQuoteTicketRepository,
        FlightQuoteManageService $flightQuoteManageService,
        FlightQuoteSegmentPaxBaggageRepository $flightQuoteSegmentPaxBaggageRepository,
        ProductQuoteOptionRepository $productQuoteOptionRepository,
        FlightQuoteOptionRepository $flightQuoteOptionRepository,
        OrderPriceUpdater $orderPriceUpdater
    ) {
        $this->paymentRepository = $paymentRepository;
        $this->productCreateService = $productCreateService;
        $this->productQuoteRepository = $productQuoteRepository;
        $this->flightQuoteRepository = $flightQuoteRepository;
        $this->flightQuoteTripRepository = $flightQuoteTripRepository;
        $this->flightQuoteSegmentRepository = $flightQuoteSegmentRepository;
        $this->productRepository = $productRepository;
        $this->flightPaxRepository = $flightPaxRepository;
        $this->flightQuotePaxPriceRepository = $flightQuotePaxPriceRepository;
        $this->flightRepository = $flightRepository;
        $this->flightQuoteFlightRepository = $flightQuoteFlightRepository;
        $this->flightQuoteBookingRepository = $flightQuoteBookingRepository;
        $this->flightQuoteTicketRepository = $flightQuoteTicketRepository;
        $this->flightQuoteManageService = $flightQuoteManageService;
        $this->flightQuoteSegmentPaxBaggageRepository = $flightQuoteSegmentPaxBaggageRepository;
        $this->productQuoteOptionRepository = $productQuoteOptionRepository;
        $this->flightQuoteOptionRepository = $flightQuoteOptionRepository;
        $this->orderPriceUpdater = $orderPriceUpdater;
    }

    public function createHandler(
        Order $order,
        OrderCreateFromSaleForm $orderCreateFromSaleForm,
        array $saleData
    ): ProductQuote {
        $product = Product::create(
            new CreateDto(null, ProductType::PRODUCT_FLIGHT, null, null, $orderCreateFromSaleForm->getProjectId())
        );
        $this->productRepository->save($product);

        $tripTypeId = self::getFlightTripIdByName(ArrayHelper::getValue($saleData, 'tripType'));
        $flightProduct = Flight::create($product->pr_id, $tripTypeId);
        if (!$flightProduct->validate()) {
            throw new \RuntimeException(ErrorsToStringHelper::extractFromModel($flightProduct, ' ', true));
        }
        $this->flightRepository->save($flightProduct);

        $productQuoteDto = new ProductQuoteCreateFromSaleDto(
            $flightProduct,
            $order->getId(),
            null,
            null,
            $order->or_client_currency
        );
        $productQuote = ProductQuote::create($productQuoteDto, null);
        $productQuote->setStatusWithEvent(self::detectProductQuoteStatus($saleData)); //TODO: set correct description;
        if (!$productQuote->validate()) {
            throw new \RuntimeException(ErrorsToStringHelper::extractFromModel($productQuote, ' ', true));
        }
        $this->productQuoteRepository->save($productQuote);

        $flightQuoteData = self::prepareFlightQuoteData($saleData, $orderCreateFromSaleForm);
        $flightQuote = FlightQuote::create((new FlightQuoteCreateDTO($flightProduct, $productQuote, $flightQuoteData, null)));
        $flightQuote->fq_flight_request_uid = $orderCreateFromSaleForm->bookingId;
        $flightQuote->fq_hash_key = null;
        $flightQuote->fq_trip_type_id = $flightProduct->fl_trip_type_id;
        $flightQuote->fq_service_fee_percent = 0;
        $this->flightQuoteRepository->save($flightQuote);

        $segmentCabin = null;

        if ($itinerary = ArrayHelper::getValue($saleData, 'itinerary')) {
            foreach ($trips = self::prepareTrips($itinerary) as $keyTrip => $trip) {
                $flightQuoteTrip = FlightQuoteTrip::create($flightQuote, (int) $trip['duration']);

                $this->flightQuoteTripRepository->save($flightQuoteTrip);
                $durationSegments = 0;

                if ($tripSegments = ArrayHelper::getValue($itinerary, "{$keyTrip}.segments")) {
                    foreach ($tripSegments as $segment) {
                        $segmentApiForm = new SegmentApiForm($keyTrip);
                        if (!$segmentApiForm->load($segment)) {
                            throw new \RuntimeException('SegmentApiForm not loaded');
                        }
                        if (!$segmentApiForm->validate()) {
                            throw new \RuntimeException(ErrorsToStringHelper::extractFromModel($segmentApiForm, ' ', true));
                        }

                        $segment['duration'] = (int) $segmentApiForm->flightDuration;
                        $segment['departureAirportCode'] = $segmentApiForm->departureAirport;
                        $segment['arrivalAirportCode'] = $segmentApiForm->arrivalAirport;
                        $segment['operatingAirline'] = $segmentApiForm->airline;
                        $segment['marketingAirline'] = $segmentApiForm->mainAirline;
                        $segment['cabin'] = FlightQuoteSegmentApiBoDto::mapCabinCalss($segmentApiForm->cabin);

                        $flightQuoteSegment = FlightQuoteSegment::create((new FlightQuoteSegmentDTO($flightQuote, $flightQuoteTrip, $segment)));
                        $flightQuoteSegment->fqs_recheck_baggage = null;
                        $this->flightQuoteSegmentRepository->save($flightQuoteSegment);

                        $flightQuoteSegmentPaxBaggage = FlightQuoteSegmentPaxBaggage::createByParams(
                            FlightPax::getPaxId(FlightPax::PAX_ADULT),
                            $flightQuoteSegment->fqs_id,
                            $segmentApiForm->carryOn,
                            $segmentApiForm->airline,
                            $segmentApiForm->baggage
                        );
                        $this->flightQuoteSegmentPaxBaggageRepository->save($flightQuoteSegmentPaxBaggage);

                        $durationSegments += (int) $segmentApiForm->flightDuration;

                        $segmentCabin = $segment['cabin'];
                    }
                }
                if ((int) $flightQuoteTrip->fqt_duration === 0) {
                    $flightQuoteTrip->fqt_duration = $durationSegments;
                    $this->flightQuoteTripRepository->save($flightQuoteTrip);
                }
            }
        }

        $bookingId = !empty($orderCreateFromSaleForm->baseBookingId) ? $orderCreateFromSaleForm->baseBookingId : $orderCreateFromSaleForm->bookingId;
        $childBookingId = ($orderCreateFromSaleForm->bookingId !== $orderCreateFromSaleForm->baseBookingId) ?
            $orderCreateFromSaleForm->bookingId : null;
        $flightQuoteFlight = FlightQuoteFlight::create(
            $flightQuote->getId(),
            $orderCreateFromSaleForm->getTripTypeId(),
            $orderCreateFromSaleForm->validatingCarrier,
            $bookingId,
            null,
            $orderCreateFromSaleForm->pnr,
            $orderCreateFromSaleForm->validatingCarrier,
            null,
            $childBookingId
        );
        if (!$flightQuoteFlight->validate()) {
            throw new \RuntimeException(ErrorsToStringHelper::extractFromModel($flightQuoteFlight, ' ', true));
        }
        $flightQuoteFlightId = $this->flightQuoteFlightRepository->save($flightQuoteFlight);

        $flightQuoteBooking = FlightQuoteBooking::create(
            $flightQuoteFlightId,
            $bookingId,
            $orderCreateFromSaleForm->pnr,
            $orderCreateFromSaleForm->getGdsId(),
            null,
            $orderCreateFromSaleForm->validatingCarrier,
            $childBookingId
        );
        if (!$flightQuoteBooking->validate()) {
            throw new \RuntimeException(ErrorsToStringHelper::extractFromModel($flightQuoteBooking, ' ', true));
        }
        $flightQuoteBookingId = $this->flightQuoteBookingRepository->save($flightQuoteBooking);

        $paxTypeCount = [];
        if ($passengers = ArrayHelper::getValue($saleData, 'passengers')) {
            foreach ($passengers as $passenger) {
                $flightPaxForm = new FlightPaxForm();
                $flightPaxForm->load($passenger);

                if ($flightPaxForm->validate()) {
                    $flightPax = FlightPax::createByParams(
                        $flightQuote->fq_flight_id,
                        $flightPaxForm->type,
                        $flightPaxForm->first_name,
                        $flightPaxForm->last_name,
                        $flightPaxForm->middle_name,
                        $flightPaxForm->birth_date,
                        $flightPaxForm->gender
                    );
                    $flightPaxId = $this->flightPaxRepository->save($flightPax);

                    if ($flightPaxForm->ticket_number) {
                        $flightQuoteTicket = FlightQuoteTicket::create($flightPaxId, $flightQuoteBookingId, $flightPaxForm->ticket_number);
                        $this->flightQuoteTicketRepository->save($flightQuoteTicket);
                    }

                    $cnt = ArrayHelper::getValue($paxTypeCount, $flightPaxForm->type, 0) + 1;
                    ArrayHelper::setValue($paxTypeCount, $flightPaxForm->type, $cnt);
                } else {
                    $warning = [
                        'message' => 'FlightPax fail create',
                        'errors' => ErrorsToStringHelper::extractFromModel($flightPaxForm, ' ', true),
                        'data' => $passenger,
                    ];
                    \Yii::warning($warning, 'FlightFromSaleService:FlightPaxForm:create');
                }
            }
        }

        foreach ($this->options as $optionKey) {
            if (self::checkOption($optionKey, $saleData)) {
                $this->createOption($optionKey, $saleData[$optionKey], $flightQuote);
            }
        }

        if (empty($flightProduct->fl_cabin_class) && !empty($segmentCabin)) {
            $flightProduct->fl_cabin_class = $segmentCabin;
        }
        $flightProduct->fl_adults = $paxTypeCount[FlightPax::PAX_ADULT] ?? 0;
        $flightProduct->fl_children = $paxTypeCount[FlightPax::PAX_CHILD] ?? 0;
        $flightProduct->fl_infants = $paxTypeCount[FlightPax::PAX_INFANT] ?? 0;
        $flightProduct->update();

        $estimationTotal = 0;
        if ($priceQuotes = ArrayHelper::getValue($saleData, 'price.priceQuotes')) {
            foreach ($priceQuotes as $paxType => $priceQuote) {
                $priceQuote['paxType'] = $paxType;
                $priceQuote['cnt'] = $priceQuote['cnt'] ?? $paxTypeCount[$paxType] ?? 1;
                $priceQuotesForm = new PriceQuotesForm();
                $priceQuotesForm->load($priceQuote);

                if (!$priceQuotesForm->validate()) {
                    throw new \RuntimeException(ErrorsToStringHelper::extractFromModel($priceQuotesForm));
                }

                $currencyRate = CurrencyHelper::getAppRateByCode($orderCreateFromSaleForm->currency);
                $flightQuotePaxPrice = new FlightQuotePaxPrice();
                $flightQuotePaxPrice->qpp_flight_pax_code_id = $priceQuotesForm->getPaxTypeId();
                $flightQuotePaxPrice->qpp_flight_quote_id = $flightQuote->getId();
                $flightQuotePaxPrice->qpp_origin_currency = $orderCreateFromSaleForm->currency;
                $flightQuotePaxPrice->qpp_fare = $priceQuotesForm->fare / $currencyRate;
                $flightQuotePaxPrice->qpp_tax = $priceQuotesForm->taxes / $currencyRate;
                $flightQuotePaxPrice->qpp_system_mark_up = $priceQuotesForm->mark_up / $currencyRate;
                $flightQuotePaxPrice->qpp_cnt = $priceQuotesForm->cnt;
                $flightQuotePaxPrice->qpp_client_currency = $orderCreateFromSaleForm->currency;

                $flightQuotePaxPrice->qpp_client_fare = $priceQuotesForm->fare;
                $flightQuotePaxPrice->qpp_client_tax = $priceQuotesForm->taxes;

                if (!$flightQuotePaxPrice->validate()) {
                    throw new \RuntimeException(ErrorsToStringHelper::extractFromModel($flightQuotePaxPrice));
                }
                $this->flightQuotePaxPriceRepository->save($flightQuotePaxPrice);

                $estimationTotal += ($priceQuotesForm->selling * $priceQuotesForm->cnt);
            }
        }

        $this->flightQuoteManageService->calcProductQuotePrice($productQuote, $flightQuote);
        $this->orderPriceUpdater->update($order->getId());

        return $productQuote;
    }

    private static function checkOption(string $optionKey, array $saleData): bool
    {
        return (array_key_exists($optionKey, $saleData) && is_array($saleData[$optionKey]) && !empty($saleData[$optionKey]));
    }

    public function createOption(
        string $optionKey,
        array $data,
        FlightQuote $flightQuote
    ): ?ProductQuoteOption {
        if (ArrayHelper::getValue($data, 'isActivated', false) !== true) {
            return null;
        }
        $optionApiForm = new OptionApiForm();
        $optionApiForm->pqo_key = $optionKey;
        $optionApiForm->pqo_name = $data['title'] ?? $optionKey;
        $optionApiForm->pqo_price = $data['amount'] ?? null;
        $optionApiForm->pqo_markup = 0.00;
        $optionApiForm->pqo_description = null;
        $optionApiForm->pqo_request_data = $data;

        if (!$optionApiForm->validate()) {
            throw new \RuntimeException(ErrorsToStringHelper::extractFromModel($optionApiForm));
        }

        $productQuoteOption = ProductQuoteOption::create(
            $flightQuote->fq_product_quote_id,
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

        return $productQuoteOption;
    }

    private static function detectProductQuoteStatus(array $saleData): int
    {
        try {
            if ((!$boStatus = $saleData['saleStatus'] ?? null) || !is_string($boStatus)) {
                throw new \RuntimeException('"saleStatus" not found in "saleData"');
            }
            $boStatus = strtolower($boStatus);
            if (!$productQuoteStatus = ProductQuoteStatus::STATUS_BO_MAP[$boStatus] ?? null) {
                throw new \RuntimeException('"saleStatus"(' . $boStatus . ') not mapped in ProductQuoteStatus');
            }
            return $productQuoteStatus;
        } catch (\Throwable $throwable) {
            Yii::warning(AppHelper::throwableLog($throwable), 'FlightFromSaleService:detectProductQuoteStatus');
            return ProductQuoteStatus::NEW;
        }
    }

    private static function prepareTrips(array $itinerary)
    {
        $trips = [];
        foreach ($itinerary as $key => $segments) {
            foreach ($segments as $keySegment => $segment) {
                $segmentDuration = ($segment['flightDuration'] ?? 0) + ($segment['layoverDuration'] ?? 0);
                $duration = ArrayHelper::getValue($trips, "{$key}.duration", 0) + $segmentDuration;
                ArrayHelper::setValue($trips, "{$key}.duration", $duration);
            }
        }
        return $trips;
    }

    public static function getFlightTripIdByName(?string $name, bool $strict = false): ?string
    {
        if (!$name) {
            return null;
        }
        if (($tripSearch = array_search($name, Flight::TRIP_TYPE_LIST, $strict)) !== false) {
            return $tripSearch;
        }
        if ($name === 'Multidestination') {
            return (string) Flight::TRIP_TYPE_MULTI_DESTINATION;
        }
        return null;
    }

    private static function prepareFlightQuoteData(array $saleData, OrderCreateFromSaleForm $orderCreateFromSaleForm): array
    {
        return [
            'recordLocator' => ArrayHelper::getValue($saleData, 'itinerary.0.segments.0.airlineRecordLocator'),
            'gds' => $orderCreateFromSaleForm->getGdsId(),
            'pcc' => $orderCreateFromSaleForm->pcc,
            'validatingCarrier' => $orderCreateFromSaleForm->validatingCarrier,
            'fareType' => $orderCreateFromSaleForm->fareType,
            'key' => ArrayHelper::getValue($saleData, 'bookingId', serialize($saleData)),
            'trips' => ArrayHelper::getValue($saleData, 'itinerary', [])
        ];
    }
}
