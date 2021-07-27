<?php

namespace modules\flight\src\useCases\sale;

use common\components\SearchService;
use modules\flight\models\Flight;
use modules\flight\models\FlightPax;
use modules\flight\models\FlightQuote;
use modules\flight\models\FlightQuoteBooking;
use modules\flight\models\FlightQuoteBookingAirline;
use modules\flight\models\FlightQuoteFlight;
use modules\flight\models\FlightQuotePaxPrice;
use modules\flight\models\FlightQuoteSegment;
use modules\flight\models\FlightQuoteTicket;
use modules\flight\models\FlightQuoteTrip;
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
use modules\order\src\payment\PaymentRepository;
use modules\product\src\entities\product\Product;
use modules\product\src\entities\product\ProductRepository;
use modules\product\src\entities\productQuote\ProductQuote;
use modules\product\src\entities\productQuote\ProductQuoteStatus;
use modules\product\src\entities\productType\ProductType;
use modules\product\src\useCases\product\create\ProductCreateForm;
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
 */
class FlightFromSaleService
{
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
        FlightQuotePaxPriceRepository $flightQuotePaxPriceRepository
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
    }

    public function create(int $orderId, int $projectId, array $saleData, ?string $currency)
    {
        $productCreateForm = new ProductCreateForm(['pr_type_id' => ProductType::PRODUCT_FLIGHT, 'pr_project_id' => $projectId]);
        $product = Product::create($productCreateForm->getDto());
        $this->productRepository->save($product);

        $flightProduct = Flight::create($product->pr_id);
        $productQuote = ProductQuote::create(new ProductQuoteCreateDTO($flightProduct, [], null), null);
        $productQuote->pq_order_id = $orderId;
        $productQuote->pq_status_id = self::detectProductQuoteStatus($saleData);
        $this->productQuoteRepository->save($productQuote);

        $data = [
            'recordLocator' => ArrayHelper::getValue($saleData, 'itinerary.0.segments.0.airlineRecordLocator'),
            'gds' => SearchService::getGDSKeyByName(ArrayHelper::getValue($saleData, 'gds')),
            'pcc' => ArrayHelper::getValue($saleData, 'pcc'),
            'validatingCarrier' => ArrayHelper::getValue($saleData, 'validatingCarrier'),
            'fareType' => ArrayHelper::getValue($saleData, 'fareType'),
        ];
        $flightQuote = FlightQuote::create((new FlightQuoteCreateDTO($flightProduct, $productQuote, $data, null)));
        $flightQuote->fq_flight_request_uid = ArrayHelper::getValue($saleData, 'bookingId');
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

                        $flightQuoteSegment = FlightQuoteSegment::create((new FlightQuoteSegmentDTO($flightQuote, $flightQuoteTrip, $segment)));
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
                    ++$paxTypeCount[$flightPaxForm->type];
                } else {
                    $warning = ['errors' => ErrorsToStringHelper::extractFromModel($flightPaxForm), 'data' => $passenger];
                    \Yii::warning($warning, 'FlightFromSaleService:FlightPaxForm:validate');
                }
            }
        }

        if ($priceQuotes = ArrayHelper::getValue($saleData, 'price.priceQuotes')) {
            foreach ($priceQuotes as $paxType => $priceQuote) {
                $priceQuote['paxType'] = $paxType;
                $priceQuote['cnt'] = $paxTypeCount[$paxType] ?? 1;
                $priceQuotesForm = new PriceQuotesForm($priceQuote);

                if ($priceQuotesForm->validate()) {
                    $flightQuotePaxPrice = new FlightQuotePaxPrice();
                    $flightQuotePaxPrice->qpp_flight_pax_code_id = $priceQuotesForm->getPaxTypeId();
                    $flightQuotePaxPrice->qpp_flight_quote_id = $flightQuote->getId();
                    $flightQuotePaxPrice->qpp_origin_currency = $currency;
                    $flightQuotePaxPrice->qpp_fare = $priceQuotesForm->fare;
                    $flightQuotePaxPrice->qpp_tax = $priceQuotesForm->taxes;
                    $flightQuotePaxPrice->qpp_system_mark_up = $priceQuotesForm->mark_up;
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
                $segmentDuration = ArrayHelper::getValue($segment, 'flightDuration', 0) + ArrayHelper::getValue($segment, 'layoverDuration', 0);
                $trips[$key]['duration'] += $segmentDuration;
            }
        }
        return $trips;
    }
}
