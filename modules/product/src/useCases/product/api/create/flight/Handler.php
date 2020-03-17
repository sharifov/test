<?php

namespace modules\product\src\useCases\product\api\create\flight;

use modules\flight\models\Flight;
use modules\flight\models\FlightSegment;
use modules\flight\src\dto\flightSegment\SegmentDTO;
use modules\flight\src\repositories\flight\FlightRepository;
use modules\flight\src\repositories\flightSegment\FlightSegmentRepository;
use modules\flight\src\services\flight\calculator\FlightTripTypeCalculator;
use modules\product\src\entities\product\dto\CreateDto;
use modules\product\src\entities\product\Product;
use modules\product\src\entities\product\ProductRepository;
use modules\product\src\entities\productType\ProductType;
use sales\services\TransactionManager;

/**
 * Class Handler
 *
 * @property TransactionManager $transactionManager
 * @property ProductRepository $productRepository
 * @property FlightRepository $flightRepository
 * @property FlightSegmentRepository $flightSegmentRepository
 */
class Handler
{
    private $transactionManager;
    private $productRepository;
    private $flightRepository;
    private $flightSegmentRepository;

    public function __construct(
        TransactionManager $transactionManager,
        ProductRepository $productRepository,
        FlightRepository $flightRepository,
        FlightSegmentRepository $flightSegmentRepository
    )
    {
        $this->transactionManager = $transactionManager;
        $this->productRepository = $productRepository;
        $this->flightRepository = $flightRepository;
        $this->flightSegmentRepository = $flightSegmentRepository;
    }

    public function handle(
        int $leadId,
        ?string $cabinClass,
        int $adults,
        int $children,
        int $infants,
        SegmentDTO ...$segments
    ): void
    {
        $this->transactionManager->wrap(function () use ($leadId, $cabinClass, $adults, $children, $infants, $segments) {

            $product = Product::create(new CreateDto($leadId, ProductType::PRODUCT_FLIGHT, null, null));
            $this->productRepository->save($product);

            $flight = Flight::createByApi(
                $product->pr_id,
                FlightTripTypeCalculator::calculate(...$segments),
                $cabinClass,
                $adults,
                $children,
                $infants
            );
            $this->flightRepository->save($flight);

            foreach ($segments as $segment) {
                $segment->flightId = $flight->fl_id;
                $flightSegment = FlightSegment::create($segment);
                $this->flightSegmentRepository->save($flightSegment);
            }

            return $product->pr_id;

        });
    }
}
