<?php

namespace modules\flight\src\services\flight;

use modules\flight\models\FlightSegment;
use modules\flight\models\forms\FlightSegmentEditForm;
use modules\flight\models\forms\FlightSegmentForm;
use modules\flight\models\forms\ItineraryEditForm;
use modules\flight\src\dto\flightSegment\SegmentDTO;
use modules\flight\src\repositories\flight\FlightRepository;
use modules\flight\src\repositories\flightSegment\FlightSegmentRepository;
use modules\flight\src\services\flight\calculator\FlightTripTypeCalculator;
use modules\product\src\entities\product\ProductRepository;
use sales\services\TransactionManager;

/**
 * Class FlightManageService
 *
 * @property FlightRepository $flightRepository
 * @property FlightSegmentRepository $segmentRepository
 * @property TransactionManager $transaction
 * @property ProductRepository $productRepository
 */
class FlightManageService
{
	private $flightRepository;
	private $transaction;
	private $segmentRepository;
    private $productRepository;

	public function __construct(
	    FlightRepository $flightRepository,
        FlightSegmentRepository $segmentRepository,
        TransactionManager $transaction,
        ProductRepository $productRepository
    )
	{
		$this->flightRepository = $flightRepository;
		$this->transaction = $transaction;
		$this->segmentRepository = $segmentRepository;
        $this->productRepository = $productRepository;
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

			$product = $flight->flProduct;
            $product->changeMarketPrice($form->pr_market_price);
            $product->changeClientBudget($form->pr_client_budget);
            $this->productRepository->save($product);

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