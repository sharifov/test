<?php

namespace modules\flight\src\useCases\sale;

use common\components\SearchService;
use modules\flight\models\Flight;
use modules\flight\models\FlightPax;
use modules\flight\models\FlightQuote;
use modules\flight\models\FlightQuotePaxPrice;
use modules\flight\models\FlightQuoteSegment;
use modules\flight\models\FlightQuoteTicket;
use modules\flight\models\FlightQuoteTrip;
use modules\flight\src\dto\flightSegment\FlightQuoteSegmentApiBoDto;
use modules\flight\src\repositories\flight\FlightRepository;
use modules\flight\src\repositories\flightPaxRepository\FlightPaxRepository;
use modules\flight\src\repositories\flightQuotePaxPriceRepository\FlightQuotePaxPriceRepository;
use modules\flight\src\repositories\flightQuoteRepository\FlightQuoteRepository;
use modules\flight\src\repositories\flightQuoteSegment\FlightQuoteSegmentRepository;
use modules\flight\src\repositories\flightQuoteTripRepository\FlightQuoteTripRepository;
use modules\flight\src\useCases\flightQuote\create\FlightQuoteCreateDTO;
use modules\flight\src\useCases\flightQuote\create\FlightQuoteSegmentDTO;
use modules\flight\src\useCases\flightQuote\create\ProductQuoteCreateDTO;
use modules\flight\src\useCases\sale\form\FlightPaxForm;
use modules\flight\src\useCases\sale\form\PriceQuotesForm;
use modules\order\src\entities\order\Order;
use modules\order\src\payment\PaymentRepository;
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
        FlightRepository $flightRepository
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
    }

    public function createHandler(Order $order, int $projectId, array $saleData, ?string $currency)
    {
        $product = Product::create(new CreateDto(null, ProductType::PRODUCT_FLIGHT, null, null, $projectId));
        $this->productRepository->save($product);

        $flightProduct = Flight::create($product->pr_id);
        $flightProduct->fl_trip_type_id = self::getFlightTripIdByName(ArrayHelper::getValue($saleData, 'tripType'));
        $this->flightRepository->save($flightProduct);

        $productQuote = ProductQuote::create(new ProductQuoteCreateDTO($flightProduct, [], null), null);
        $productQuote->pq_order_id = $order->getId();
        $productQuote->pq_status_id = self::detectProductQuoteStatus($saleData);
        $productQuote->pq_origin_price = $order->or_app_total;
        $productQuote->pq_origin_currency = $order->or_client_currency;
        $productQuote->pq_client_price = $order->or_client_total;
        $this->productQuoteRepository->save($productQuote);

        $data = [
            'recordLocator' => ArrayHelper::getValue($saleData, 'itinerary.0.segments.0.airlineRecordLocator'),
            'gds' => SearchService::getGDSKeyByName(ArrayHelper::getValue($saleData, 'gds')),
            'pcc' => ArrayHelper::getValue($saleData, 'pcc'),
            'validatingCarrier' => ArrayHelper::getValue($saleData, 'validatingCarrier'),
            'fareType' => ArrayHelper::getValue($saleData, 'fareType'),
            'key' => md5(serialize($saleData)),
            'trips' => ArrayHelper::getValue($saleData, 'itinerary', [])
        ];
        $flightQuote = FlightQuote::create((new FlightQuoteCreateDTO($flightProduct, $productQuote, $data, null)));
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
                    $this->flightPaxRepository->save($flightPax);
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

        if ($priceQuotes = ArrayHelper::getValue($saleData, 'price.priceQuotes')) {
            foreach ($priceQuotes as $paxType => $priceQuote) {
                $priceQuote['paxType'] = $paxType;
                $priceQuote['cnt'] = $paxTypeCount[$paxType] ?? 1;
                $priceQuotesForm = new PriceQuotesForm();
                $priceQuotesForm->load($priceQuote);

                if ($priceQuotesForm->validate()) {
                    $flightQuotePaxPrice = new FlightQuotePaxPrice();
                    $flightQuotePaxPrice->qpp_flight_pax_code_id = $priceQuotesForm->getPaxTypeId();
                    $flightQuotePaxPrice->qpp_flight_quote_id = $flightQuote->getId();
                    $flightQuotePaxPrice->qpp_origin_currency = $currency;
                    $flightQuotePaxPrice->qpp_fare = $priceQuotesForm->fare;
                    $flightQuotePaxPrice->qpp_tax = $priceQuotesForm->taxes;
                    $flightQuotePaxPrice->qpp_system_mark_up = $priceQuotesForm->mark_up;
                    $flightQuotePaxPrice->qpp_cnt = $priceQuotesForm->cnt;
                    $flightQuotePaxPrice->qpp_client_currency = $currency;

                    if ($flightQuotePaxPrice->validate()) {
                        $this->flightQuotePaxPriceRepository->save($flightQuotePaxPrice);
                    } else {
                        $warning = ['errors' => ErrorsToStringHelper::extractFromModel($flightQuotePaxPrice), 'data' => $priceQuotesForm->getAttributes()];
                        \Yii::warning($warning, 'FlightFromSaleService:FlightQuotePaxPrice:validate');
                    }
                } else {
                    $warning = ['errors' => ErrorsToStringHelper::extractFromModel($priceQuotesForm), 'data' => $priceQuote];
                    \Yii::warning($warning, 'FlightFromSaleService:FlightQuotePaxPrice:validate');
                }
            }
        }
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
}
