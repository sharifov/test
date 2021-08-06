<?php

namespace modules\flight\src\useCases\sale;

use common\components\SearchService;
use modules\flight\models\Flight;
use modules\flight\models\FlightPax;
use modules\flight\models\FlightQuote;
use modules\flight\models\FlightQuoteBooking;
use modules\flight\models\FlightQuoteFlight;
use modules\flight\models\FlightQuotePaxPrice;
use modules\flight\models\FlightQuoteSegment;
use modules\flight\models\FlightQuoteTicket;
use modules\flight\models\FlightQuoteTrip;
use modules\flight\src\dto\flightSegment\FlightQuoteSegmentApiBoDto;
use modules\flight\src\repositories\flight\FlightRepository;
use modules\flight\src\repositories\flightPaxRepository\FlightPaxRepository;
use modules\flight\src\repositories\flightQuoteBooking\FlightQuoteBookingRepository;
use modules\flight\src\repositories\flightQuoteFlight\FlightQuoteFlightRepository;
use modules\flight\src\repositories\flightQuotePaxPriceRepository\FlightQuotePaxPriceRepository;
use modules\flight\src\repositories\flightQuoteRepository\FlightQuoteRepository;
use modules\flight\src\repositories\flightQuoteSegment\FlightQuoteSegmentRepository;
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
use modules\product\src\entities\product\dto\CreateDto;
use modules\product\src\entities\product\Product;
use modules\product\src\entities\product\ProductRepository;
use modules\product\src\entities\productQuote\ProductQuote;
use modules\product\src\entities\productQuote\ProductQuoteStatus;
use modules\product\src\entities\productType\ProductType;
use modules\product\src\useCases\product\create\ProductCreateService;
use sales\helpers\ErrorsToStringHelper;
use sales\repositories\product\ProductQuoteRepository;
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
        FlightQuoteManageService $flightQuoteManageService
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
            throw new \RuntimeException(ErrorsToStringHelper::extractFromModel($flightProduct));
        }
        $this->flightRepository->save($flightProduct);

        $productQuoteDto = new ProductQuoteCreateFromSaleDto(
            $flightProduct,
            $order->getId(),
            null, //$order->or_app_total, /* TODO::  */
            null, //$order->or_app_total, /* TODO::  */
            $order->or_client_currency
        );
        $productQuote = ProductQuote::create($productQuoteDto, null);
        $productQuote->pq_status_id = self::detectProductQuoteStatus($saleData);
        if (!$productQuote->validate()) {
            throw new \RuntimeException(ErrorsToStringHelper::extractFromModel($productQuote));
        }
        $this->productQuoteRepository->save($productQuote);

        $flightQuoteData = self::prepareFlightQuoteData($saleData, $orderCreateFromSaleForm);
        $flightQuote = FlightQuote::create((new FlightQuoteCreateDTO($flightProduct, $productQuote, $flightQuoteData, null)));
        $flightQuote->fq_flight_request_uid = ArrayHelper::getValue($saleData, 'bookingId');
        $flightQuote->fq_hash_key = null;
        $flightQuote->fq_trip_type_id = $flightProduct->fl_trip_type_id;
        $flightQuote->fq_service_fee_percent = 0;
        $this->flightQuoteRepository->save($flightQuote);

        if ($itinerary = ArrayHelper::getValue($saleData, 'itinerary')) {
            foreach ($trips = self::prepareTrips($itinerary) as $keyTrip => $trip) {
                $flightQuoteTrip = FlightQuoteTrip::create($flightQuote, (int) $trip['duration']);

                $this->flightQuoteTripRepository->save($flightQuoteTrip);

                if ($tripSegments = ArrayHelper::getValue($itinerary, "{$keyTrip}.segments")) {
                    foreach ($tripSegments as $segment) {
                        $segment['duration'] = ArrayHelper::getValue($segment, 'flightDuration', 0) + ArrayHelper::getValue($segment, 'layoverDuration', 0);
                        $segment['departureAirportCode'] = $segment['departureAirport'];
                        $segment['arrivalAirportCode'] = $segment['arrivalAirport'];
                        $segment['operatingAirline'] = $segment['airline'];
                        $segment['marketingAirline'] = $segment['mainAirline'];
                        $segment['cabin'] = FlightQuoteSegmentApiBoDto::mapCabinCalss(ArrayHelper::getValue($segment, 'cabin'));

                        $flightQuoteSegment = FlightQuoteSegment::create((new FlightQuoteSegmentDTO($flightQuote, $flightQuoteTrip, $segment)));
                        $flightQuoteSegment->fqs_recheck_baggage = null;
                        $this->flightQuoteSegmentRepository->save($flightQuoteSegment);
                    }
                }
            }
        }

        $flightQuoteFlight = FlightQuoteFlight::create(
            $flightQuote->getId(),
            $orderCreateFromSaleForm->getTripTypeId(),
            $orderCreateFromSaleForm->validatingCarrier,
            $orderCreateFromSaleForm->bookingId,
            null,
            $orderCreateFromSaleForm->pnr,
            $orderCreateFromSaleForm->validatingCarrier,
            null
        );
        if (!$flightQuoteFlight->validate()) {
            throw new \RuntimeException(ErrorsToStringHelper::extractFromModel($flightQuoteFlight));
        }
        $flightQuoteFlightId = $this->flightQuoteFlightRepository->save($flightQuoteFlight);

        $flightQuoteBooking = FlightQuoteBooking::create(
            $flightQuoteFlightId,
            $orderCreateFromSaleForm->bookingId,
            $orderCreateFromSaleForm->pnr,
            $orderCreateFromSaleForm->getGdsId(),
            null,
            $orderCreateFromSaleForm->validatingCarrier
        );
        if (!$flightQuoteBooking->validate()) {
            throw new \RuntimeException(ErrorsToStringHelper::extractFromModel($flightQuoteBooking));
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
                    $warning = ['errors' => ErrorsToStringHelper::extractFromModel($flightPaxForm), 'data' => $passenger];
                    \Yii::warning($warning, 'FlightFromSaleService:FlightPaxForm:validate');
                }
            }
        }

        $flightProduct->fl_adults = $paxTypeCount[FlightPax::PAX_ADULT] ?? 0;
        $flightProduct->fl_children = $paxTypeCount[FlightPax::PAX_CHILD] ?? 0;
        $flightProduct->fl_infants = $paxTypeCount[FlightPax::PAX_INFANT] ?? 0;
        $flightProduct->update();

        $estimationTotal = 0;
        if ($priceQuotes = ArrayHelper::getValue($saleData, 'price.priceQuotes')) {
            foreach ($priceQuotes as $paxType => $priceQuote) {
                $priceQuote['paxType'] = $paxType;
                $priceQuote['cnt'] = $paxTypeCount[$paxType] ?? 1;
                $priceQuotesForm = new PriceQuotesForm();
                $priceQuotesForm->load($priceQuote);

                if (!$priceQuotesForm->validate()) {
                    throw new \RuntimeException(ErrorsToStringHelper::extractFromModel($priceQuotesForm));
                }

                $flightQuotePaxPrice = new FlightQuotePaxPrice();
                $flightQuotePaxPrice->qpp_flight_pax_code_id = $priceQuotesForm->getPaxTypeId();
                $flightQuotePaxPrice->qpp_flight_quote_id = $flightQuote->getId();
                $flightQuotePaxPrice->qpp_origin_currency = $orderCreateFromSaleForm->currency;
                $flightQuotePaxPrice->qpp_fare = $priceQuotesForm->fare;
                $flightQuotePaxPrice->qpp_tax = $priceQuotesForm->taxes;
                $flightQuotePaxPrice->qpp_system_mark_up = $priceQuotesForm->mark_up;
                $flightQuotePaxPrice->qpp_cnt = $priceQuotesForm->cnt;
                $flightQuotePaxPrice->qpp_client_currency = $orderCreateFromSaleForm->currency;

                if (!$flightQuotePaxPrice->validate()) {
                    throw new \RuntimeException(ErrorsToStringHelper::extractFromModel($flightQuotePaxPrice));
                }
                $this->flightQuotePaxPriceRepository->save($flightQuotePaxPrice);

                $estimationTotal += ($priceQuotesForm->selling * $priceQuotesForm->cnt);
            }
        }

        $this->flightQuoteManageService->calcProductQuotePrice($productQuote, $flightQuote);

        return $productQuote;
    }

    private static function detectProductQuoteStatus(array $saleData): int
    {
        return (ArrayHelper::getValue($saleData, 'passengers.0.ticket_number')) ? ProductQuoteStatus::SOLD : ProductQuoteStatus::BOOKED;
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
