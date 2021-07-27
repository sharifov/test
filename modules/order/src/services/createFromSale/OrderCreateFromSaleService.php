<?php

namespace modules\order\src\services\createFromSale;

use common\components\SearchService;
use common\models\Payment;
use modules\flight\models\Flight;
use modules\flight\models\FlightPax;
use modules\flight\models\FlightQuote;
use modules\flight\models\FlightQuotePaxPrice;
use modules\flight\models\FlightQuoteSegment;
use modules\flight\models\FlightQuoteStatusLog;
use modules\flight\models\FlightQuoteTicket;
use modules\flight\models\FlightQuoteTrip;
use modules\flight\src\repositories\flightQuoteRepository\FlightQuoteRepository;
use modules\flight\src\repositories\flightQuoteSegment\FlightQuoteSegmentRepository;
use modules\flight\src\repositories\flightQuoteTripRepository\FlightQuoteTripRepository;
use modules\flight\src\useCases\flightQuote\create\FlightQuoteCreateDTO;
use modules\flight\src\useCases\flightQuote\create\FlightQuoteSegmentDTO;
use modules\flight\src\useCases\flightQuote\create\ProductQuoteCreateDTO;
use modules\order\src\entities\order\Order;
use modules\order\src\entities\order\OrderSourceType;
use modules\order\src\entities\order\OrderStatus;
use modules\order\src\payment\helpers\PaymentHelper;
use modules\order\src\payment\method\PaymentMethodRepository;
use modules\order\src\payment\PaymentRepository;
use modules\order\src\services\CreateOrderDTO;
use modules\product\src\entities\product\Product;
use modules\product\src\entities\product\ProductRepository;
use modules\product\src\entities\productQuote\ProductQuote;
use modules\product\src\entities\productQuote\ProductQuoteStatus;
use modules\product\src\entities\productType\ProductType;
use modules\product\src\entities\productType\ProductTypeRepository;
use modules\product\src\useCases\product\create\ProductCreateForm;
use modules\product\src\useCases\product\create\ProductCreateService;
use sales\repositories\product\ProductQuoteRepository;
use yii\helpers\ArrayHelper;

/**
 * Class OrderCreateFromSaleService
 *
 * @property PaymentRepository $paymentRepository
 * @property ProductCreateService $productCreateService
 * @property ProductQuoteRepository $productQuoteRepository
 * @property FlightQuoteRepository $flightQuoteRepository
 * @property FlightQuoteTripRepository $flightQuoteTripRepository
 * @property FlightQuoteSegmentRepository $flightQuoteSegmentRepository
 * @property ProductRepository $productRepository
 */
class OrderCreateFromSaleService
{
    private PaymentRepository $paymentRepository;
    private ProductCreateService $productCreateService;
    private ProductQuoteRepository $productQuoteRepository;
    private FlightQuoteRepository $flightQuoteRepository;
    private FlightQuoteTripRepository $flightQuoteTripRepository;
    private FlightQuoteSegmentRepository $flightQuoteSegmentRepository;
    private ProductRepository $productRepository;

    /**
     * @param PaymentRepository $paymentRepository
     * @param ProductCreateService $productCreateService
     * @param ProductQuoteRepository $productQuoteRepository
     * @param FlightQuoteRepository $flightQuoteRepository
     * @param FlightQuoteTripRepository $flightQuoteTripRepository
     * @param FlightQuoteSegmentRepository $flightQuoteSegmentRepository
     * @param ProductRepository $productRepository
     */
    public function __construct(
        PaymentRepository $paymentRepository,
        ProductCreateService $productCreateService,
        ProductQuoteRepository $productQuoteRepository,
        FlightQuoteRepository $flightQuoteRepository,
        FlightQuoteTripRepository $flightQuoteTripRepository,
        FlightQuoteSegmentRepository $flightQuoteSegmentRepository,
        ProductRepository $productRepository
    ) {
        $this->paymentRepository = $paymentRepository;
        $this->productCreateService = $productCreateService;
        $this->productQuoteRepository = $productQuoteRepository;
        $this->flightQuoteRepository = $flightQuoteRepository;
        $this->flightQuoteTripRepository = $flightQuoteTripRepository;
        $this->flightQuoteSegmentRepository = $flightQuoteSegmentRepository;
        $this->productRepository = $productRepository;
    }

    public function orderCreate(OrderCreateFromSaleForm $form, int $saleId): Order
    {
        $dto = new CreateOrderDTO(
            null,
            $form->currency,
            [],
            OrderSourceType::SALE,
            null,
            $form->getProjectId(),
            OrderStatus::COMPLETE,
            null,
            null,
            null,
            $saleId
        );
        return (new Order())->create($dto);
    }

    public function paymentCreate(array $authList, int $orderId): array
    {
        $result = [];
        foreach ($authList as $value) {
            $payment = Payment::create(
                null,
                ArrayHelper::getValue($value, 'created'),
                ArrayHelper::getValue($value, 'amount'),
                null,
                null,
                $orderId,
                null,
                ArrayHelper::getValue($value, 'message'),
                null
            );
            $payment->setStatus(PaymentHelper::detectStatusFromSale(ArrayHelper::getValue($value, 'status')));
            if (!$payment->validate()) {
                $paymentWarning = $payment->getErrors();
                $paymentWarning['data'] = $value;
                \Yii::warning($paymentWarning, 'OrderCreateFromSaleService:PaymentCreate');
            } else {
                $this->paymentRepository->save($payment);
                $result[] = $payment;
            }
        }
        return $result;
    }

    public function productFlightCreate(int $orderId, int $projectId, array $saleData) /* TODO:: to FlightQuoteManageService->saleHandle */
    {
        /* TODO:: add check validate before save */

        $productCreateForm = new ProductCreateForm(['pr_type_id' => ProductType::PRODUCT_FLIGHT, 'pr_project_id' => $projectId]);
        $product = Product::create($productCreateForm->getDto());

        $this->productRepository->save($product);

        $flightProduct = Flight::create($product->pr_id);
        $productQuote = ProductQuote::create(new ProductQuoteCreateDTO($flightProduct, [], null), null);
        $productQuote->pq_order_id = $orderId;
        $productQuote->pq_status_id = ProductQuoteStatus::SOLD; /* TODO:: ??? */

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

        if ($passengers = ArrayHelper::getValue($saleData, 'passengers')) {

        }

        /* TODO:: detect currency: from price.currency */
        /* TODO::
            detect client:
            'phone' => '+1 8885328250'
            'email' => 'sf_d__7ljr@mailinator.com'
        */

        // FlightPax
        // FlightQuotePaxPrice
        // FlightQuoteTicket
        /* TODO::  */
    }

    public static function prepareTrips(array $itinerary)
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
