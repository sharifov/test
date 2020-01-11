<?php

namespace modules\flight\src\dto\flightSegment;

use modules\flight\models\forms\FlightSegmentForm;

/**
 * Class SegmentDTO
 * @package modules\flight\src\dto\flightSegment
 *
 * @property int|null $flightId
 * @property string|null $origin
 * @property string|null $destination
 * @property int|null $flexTypeId
 * @property int|null $flexDays
 * @property string|null $originLabel
 * @property string|null $destinationLabel
 * @property string|null $departure
 */
class SegmentDTO
{
	public $flightId;

	public $origin;

	public $destination;

	public $flexTypeId;

	public $flexDays;

	public $originLabel;

	public $destinationLabel;

	public $departure;

	/**
	 * @param int|null $flightId
	 * @param string|null $origin
	 * @param string|null $destination
	 * @param string|null $departure
	 * @param int|null $flexTypeId
	 * @param int|null $flexDays
	 * @param string|null $originLabel
	 * @param string|null $destinationLabel
	 */
	public function __construct(
		?int $flightId = null,
		?string $origin = '',
		?string $destination = '',
		?int $flexTypeId = null,
		?int $flexDays = null,
		?string $originLabel = '',
		?string $destinationLabel = '',
		?string $departure = ''
	) {
		$this->flightId = $flightId;
		$this->origin = $origin;
		$this->destination = $destination;
		$this->flexTypeId = $flexTypeId;
		$this->flexDays = $flexDays;
		$this->originLabel = $originLabel;
		$this->destinationLabel = $destinationLabel;
		$this->departure = $departure;
	}

	/**
	 * @param FlightSegmentForm $form
	 * @return $this
	 */
	public function fillBySegmentForm(FlightSegmentForm $form): self
	{
		$this->flightId = (int)$form->fs_flight_id;
		$this->origin = $form->fs_origin_iata;
		$this->destination = $form->fs_destination_iata;
		$this->flexTypeId = (int)$form->fs_flex_type_id;
		$this->flexDays = (int)$form->fs_flex_days;
		$this->originLabel = $form->fs_origin_iata_label;
		$this->destinationLabel = $form->fs_destination_iata_label;
		$this->departure = $form->fs_departure_date;

		return $this;
	}

}