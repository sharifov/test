<?php

namespace sales\forms\lead;

use common\models\Airports;
use common\models\LeadFlightSegment;
use sales\helpers\app\AppHelper;
use sales\repositories\airport\AirportRepository;
use Yii;

/**
 * Class SegmentEditForm
 * @property integer $segmentId
 */
class SegmentEditForm extends SegmentForm
{
    public $segmentId;

    /**
     * SegmentEditForm constructor.
     * @param LeadFlightSegment $segment
     * @param array $config
     */
    public function __construct(LeadFlightSegment $segment, array $config = [])
    {
        if (!$segment->getIsNewRecord()) {
            $this->departure = $segment->departure;
            $this->destination = $segment->destination;
            $this->origin = $segment->origin;
            $destAirport = $this->loadAirport($this->destination);
            $originAirport = $this->loadAirport($this->origin);

            $this->segmentId = $segment->id;
            $this->originLabel = $originAirport ? $this->loadAirportLabel($originAirport) : '';
            $this->originCity = $originAirport ? $this->loadCityName($originAirport) : '';
            $this->destinationLabel = $destAirport ? $this->loadAirportLabel($destAirport) : '';
            $this->destinationCity = $destAirport ? $this->loadCityName($destAirport) : '';
            $this->flexibility = $segment->flexibility;
            $this->flexibilityType = $segment->flexibility_type;
        }
        parent::__construct($config);
    }

    /**
     * @param string $iata
     * @return Airports|null
     */
    private function loadAirport(string $iata): ?Airports
    {
        try {
            return (new AirportRepository())->findByIata($iata);
        } catch (\Throwable $throwable) {
            $logMessage = AppHelper::throwableLog($throwable);
            $logMessage['airport_iata'] = $iata;
            \Yii::warning(
                $logMessage,
                'SegmentEditForm:loadAirport'
            );
            return null;
        }
    }

    /**
     * @param Airports $airportModel
     * @return string
     */
    private function loadAirportLabel(Airports $airportModel): string
    {
        try {
            return $airportModel->getSelection();
        } catch (\Throwable $throwable) {
            $logMessage = AppHelper::throwableLog($throwable);
            $logMessage['airport_iata'] = $iata;
            \Yii::warning(
                $logMessage,
                'SegmentEditForm:loadAirportLabel'
            );
            return '';
        }
    }

    /**
     * @param Airports $airportModel
     * @return string
     */
    private function loadCityName(Airports $airportModel): string
    {
        try {
            return $airportModel->getCityName();
        } catch (\Throwable $throwable) {
            $logMessage = AppHelper::throwableLog($throwable);
            $logMessage['airport_iata'] = $iata;
            \Yii::warning(
                $logMessage,
                'SegmentEditForm:loadCityName'
            );
            return '';
        }
    }
}
