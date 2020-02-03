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
use sales\services\TransactionManager;

/**
 * Class FlightManageService
 * @package modules\flight\src\services
 *
 * @property FlightRepository $flightRepository
 * @property FlightSegmentRepository $segmentRepository
 * @property TransactionManager $transaction
 */
class FlightManageService
{
	/**
	 * @var FlightRepository
	 */
	private $flightRepository;
	/**
	 * @var TransactionManager
	 */
	private $transaction;
	/**
	 * @var FlightSegmentRepository
	 */
	private $segmentRepository;

	/**
	 * FlightManageService constructor.
	 * @param FlightRepository $flightRepository
	 * @param FlightSegmentRepository $segmentRepository
	 * @param TransactionManager $transaction
	 */
	public function __construct(FlightRepository $flightRepository, FlightSegmentRepository $segmentRepository, TransactionManager $transaction)
	{
		$this->flightRepository = $flightRepository;
		$this->transaction = $transaction;
		$this->segmentRepository = $segmentRepository;
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
			$form->infants
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

	/**
	 * @param array $segments
	 * @return string
	 */
	private function calculateTripType(array $segments): string
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