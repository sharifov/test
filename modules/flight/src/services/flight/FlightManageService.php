<?php

namespace modules\flight\src\services\flight;

use common\models\Lead;
use modules\flight\models\Flight;
use modules\flight\models\FlightPax;
use modules\flight\models\FlightSegment;
use modules\flight\models\forms\FlightSegmentEditForm;
use modules\flight\models\forms\FlightSegmentForm;
use modules\flight\models\forms\ItineraryEditForm;
use modules\flight\src\dto\flightSegment\SegmentDTO;
use modules\flight\src\dto\itineraryDump\ItineraryDumpDTO;
use modules\flight\src\repositories\flight\FlightRepository;
use modules\flight\src\repositories\flightSegment\FlightSegmentRepository;
use modules\flight\src\services\flight\calculator\FlightTripTypeCalculator;
use modules\flight\src\useCases\flightQuote\createManually\FlightQuoteCreateForm;
use modules\flight\src\useCases\flightQuote\FlightQuoteManageService;
use modules\product\src\entities\product\ProductRepository;
use modules\product\src\entities\productType\ProductType;
use modules\product\src\useCases\product\create\ProductCreateForm;
use modules\product\src\useCases\product\create\ProductCreateService;
use sales\services\TransactionManager;

/**
 * Class FlightManageService
 *
 * @property FlightRepository $flightRepository
 * @property FlightSegmentRepository $segmentRepository
 * @property ProductCreateService $productCreateService
 * @property ProductRepository $productRepository
 * @property FlightSegmentRepository $flightSegmentRepository
 * @property FlightQuoteManageService $flightQuoteManageService
 * @property TransactionManager $transaction
 */
class FlightManageService
{
	private $flightRepository;
	private $transaction;
	private $segmentRepository;
	/**
	 * @var ProductCreateService
	 */
	private $productCreateService;
	/**
	 * @var ProductRepository
	 */
	private $productRepository;
	/**
	 * @var FlightSegmentRepository
	 */
	private $flightSegmentRepository;
	/**
	 * @var FlightQuoteManageService
	 */
	private $flightQuoteManageService;

	public function __construct(
	    FlightRepository $flightRepository,
        FlightSegmentRepository $segmentRepository,
        ProductCreateService $productCreateService,
        ProductRepository $productRepository,
        FlightSegmentRepository $flightSegmentRepository,
        FlightQuoteManageService $flightQuoteManageService,
        TransactionManager $transaction
    )
	{
		$this->flightRepository = $flightRepository;
		$this->transaction = $transaction;
		$this->segmentRepository = $segmentRepository;
		$this->productCreateService = $productCreateService;
		$this->productRepository = $productRepository;
		$this->flightSegmentRepository = $flightSegmentRepository;
		$this->flightQuoteManageService = $flightQuoteManageService;
	}

	/**
	 * @param int $id
	 * @param ItineraryEditForm $form
	 * @throws \Throwable
	 */
	public function editItinerary(int $id, ItineraryEditForm $form): void
	{
		$flight = $this->flightRepository->find($id);

		$flight->editItinerary(
			$form->cabin,
			$form->adults,
			$form->children,
			$form->infants,
            $form->fl_stops,
            $form->fl_delayed_charge
		);

		$this->transaction->wrap(function () use ($flight, $form) {

			$flight->setTripType(self::calculateTripType($form->segments));
			$newSegmentsIds = [];
			foreach ($form->segments as $segmentForm) {
				$segment = $this->getSegment($flight->fl_id, $segmentForm);
				$newSegmentsIds[] = $this->segmentRepository->save($segment);
			}
			$this->segmentRepository->removeOld($flight->flightSegments, $newSegmentsIds);

			$this->flightRepository->save($flight);

			$flight->updateLastAction();

		});
	}

	public function createNewProductAndAssignNewQuote(FlightQuoteCreateForm $form, Lead $lead, array $quote): void
	{
		$this->transaction->wrap(function () use ($form, $lead, $quote) {
			$productCreateForm = new ProductCreateForm();
			$productCreateForm->pr_lead_id = $lead->id;
			$productCreateForm->pr_type_id = ProductType::PRODUCT_FLIGHT;
			$newProductId = $this->productCreateService->create($productCreateForm);
			$product = $this->productRepository->find($newProductId);

			$newFlight = $product->flight;
			$newFlight->editItinerary(
				Flight::getCabinByRealCode($quote['cabin']),
				$quote['passengers'][FlightPax::PAX_ADULT]['cnt'] ?? 0,
				$quote['passengers'][FlightPax::PAX_CHILD]['cnt'] ?? 0,
				$quote['passengers'][FlightPax::PAX_INFANT]['cnt'] ?? 0,
				count($form->itinerary) - 1,
				false
			);
			$newFlight->setTripType($quote['tripType']);
			$this->flightRepository->save($newFlight);

			/** @var ItineraryDumpDTO $itinerary */
			foreach ($form->itinerary as $itinerary) {
				$segmentDto = (new SegmentDTO())->fillByItineraryDumpDto($itinerary, $newFlight->fl_id);
				$flightSegment = FlightSegment::create($segmentDto);
				$this->flightSegmentRepository->save($flightSegment);
			}
			$this->flightQuoteManageService->create($newFlight, $quote, $form->quoteCreator);
		});
	}

	public function updateFlightRequestAndAssignNewQuote(FlightQuoteCreateForm $form, Flight $flight, array $quote): void
	{
		$this->transaction->wrap(function () use ($form, $flight, $quote) {
			$flight->editItinerary(
				Flight::getCabinByRealCode($quote['cabin']),
				$quote['passengers'][FlightPax::PAX_ADULT]['cnt'] ?? 0,
				$quote['passengers'][FlightPax::PAX_CHILD]['cnt'] ?? 0,
				$quote['passengers'][FlightPax::PAX_INFANT]['cnt'] ?? 0,
				count($form->itinerary) - 1,
				false
			);
			$flight->setTripType($quote['tripType']);
			$this->flightRepository->save($flight);
			foreach ($flight->flightSegments as $segment) {
				$this->flightSegmentRepository->remove($segment);
			}
			/** @var ItineraryDumpDTO $itinerary */
			foreach ($form->itinerary as $itinerary) {
				$segmentDto = (new SegmentDTO())->fillByItineraryDumpDto($itinerary, $flight->fl_id);
				$flightSegment = FlightSegment::create($segmentDto);
				$this->flightSegmentRepository->save($flightSegment);
			}
			$this->flightQuoteManageService->create($flight, $quote, $form->quoteCreator);
		});
	}

	/**
	 * @param array $segments
	 * @return int|null
	 * @throws \yii\base\InvalidConfigException
	 */
	private function calculateTripType(array $segments): ?int
	{
		$segmentsDTO = [];

		/** @var FlightSegmentForm $segment */
		foreach ($segments as $segment) {
			$segmentsDTO[] = new SegmentDTO(null, $segment->fs_origin_iata, $segment->fs_destination_iata);
		}

		return FlightTripTypeCalculator::calculate(...$segmentsDTO);
	}

	/**
	 * @param int $flightId
	 * @param FlightSegmentEditForm $segmentForm
	 * @return FlightSegment
	 */
	private function getSegment(int $flightId, FlightSegmentEditForm $segmentForm): FlightSegment
	{
		$dto = (new SegmentDTO())->fillBySegmentForm($segmentForm);

		if ($segmentForm->fs_id) {
			$segment = $this->segmentRepository->find($segmentForm->fs_id);
			$segment->edit($dto);
			return $segment;
		}

		$dto->flightId = $flightId;
		$segment = FlightSegment::create($dto);
		return $segment;
	}
}