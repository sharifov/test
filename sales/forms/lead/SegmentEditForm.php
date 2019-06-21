<?php

namespace sales\forms\lead;

use common\models\LeadFlightSegment;
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
    public function __construct(LeadFlightSegment $segment, $config = [])
    {
        if (!$segment->getIsNewRecord()) {
            $this->segmentId = $segment->id;
            $this->origin = $segment->origin;
            $this->originLabel = $this->loadAirportLabel($this->origin);
            $this->originCity = $this->loadCityName($this->origin);
            $this->destination = $segment->destination;
            $this->destinationLabel = $this->loadAirportLabel($this->destination);
            $this->destinationCity = $this->loadCityName($this->destination);
            $this->departure = $segment->departure;
            $this->flexibility = $segment->flexibility;
            $this->flexibilityType = $segment->flexibility_type;
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
            return (new AirportRepository())->getByIata($iata)->getSelection();
        } catch (\Exception $e) {
            Yii::$app->errorHandler->logException($e);
            return '';
        }
    }

    /**
     * @param string $iata
     * @return string
     */
    private function loadCityName(string $iata): string
    {
        try {
            return (new AirportRepository())->getByIata($iata)->getCityName();
        } catch (\Exception $e) {
            Yii::$app->errorHandler->logException($e);
            return '';
        }
    }
}
