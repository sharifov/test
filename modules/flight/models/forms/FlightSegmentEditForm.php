<?php

namespace modules\flight\models\forms;

use modules\flight\models\FlightSegment;
use sales\repositories\airport\AirportRepository;
use Yii;

class FlightSegmentEditForm extends FlightSegmentForm
{
	/**
	 * FlightSegmentEditForm constructor.
	 * @param FlightSegment $flightSegment
	 * @param array $config
	 */
	public function __construct(FlightSegment $flightSegment, $config = [])
	{
		if (!$flightSegment->getIsNewRecord()) {
			$this->fs_id = $flightSegment->fs_id;
			$this->fs_flight_id = $flightSegment->fs_flight_id;
			$this->fs_origin_iata = $flightSegment->fs_origin_iata;
			$this->fs_destination_iata = $flightSegment->fs_destination_iata;
			$this->fs_origin_iata_label = $this->loadAirportLabel($this->fs_origin_iata);
			$this->fs_destination_iata_label = $this->loadAirportLabel($this->fs_destination_iata);
			$this->fs_departure_date = $flightSegment->fs_departure_date;
			$this->fs_flex_days = $flightSegment->fs_flex_days;
			$this->fs_flex_type_id = $flightSegment->fs_flex_type_id;
		}
		parent::__construct($config);
	}

	/**
	 * @param string $iata
	 * @return string
	 */
	private function loadAirportLabel(string $iata): string
	{
		try {
			return (new AirportRepository())->findByIata($iata)->getSelection();
		} catch (\Exception $e) {
			Yii::$app->errorHandler->logException($e);
			return '';
		}
	}
}