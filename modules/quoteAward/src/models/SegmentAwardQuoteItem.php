<?php

namespace modules\quoteAward\src\models;

use common\models\Airports;
use common\models\Lead;
use src\helpers\app\AppHelper;
use src\repositories\airport\AirportRepository;
use yii\base\Model;

class SegmentAwardQuoteItem extends Model
{
    public const TRIP_COUNT = 10;

    public $origin;
    public $destination;
    public $departure;
    public $arrival;
    public $trip;
    public $flight;
    public $flight_number;
    public $cabin;
    public $operatedBy;

    public $originLabel;
    public $destinationLabel;

    public function rules(): array
    {
        return [
            [['origin', 'destination', 'departure',
                'arrival', 'trip', 'flight', 'flight_number', 'cabin', 'operatedBy', 'originLabel', 'destinationLabel'], 'safe'],
        ];
    }


    public function __construct(?Lead $lead = null, int $tripId = 1, $config = [])
    {
        $this->trip = $tripId;
        $this->flight = 0;

        if ($lead) {
            $this->cabin = $lead->cabin;
        }
        parent::__construct($config);
    }

    public function setParams($params)
    {
        $this->setAttributes($params);
        $this->setAirportLabel();
    }

    public function loadData(array $segment, int $trip)
    {
        $this->origin = $segment['departureAirport'] ?? null;
        $this->destination = $segment['arrivalAirport'] ?? null;

        if (array_key_exists('departureDateTime', $segment) && $segment['departureDateTime'] instanceof \DateTime) {
            $this->departure = $segment['departureDateTime']->format('Y-m-d H:i');
        }

        if (array_key_exists('arrivalDateTime', $segment) && $segment['arrivalDateTime'] instanceof \DateTime) {
            $this->arrival = $segment['arrivalDateTime']->format('Y-m-d H:i');
        }
        $this->trip = $trip;
        $this->flight = 0;
        $this->flight_number = $segment['flightNumber'] ?? null;
        $this->cabin = $segment['cabin'] ?? null;
        $this->operatedBy = $segment['carrier'] ?? null;
        $this->setAirportLabel();
    }

    private function setAirportLabel()
    {
        $destAirport = $this->loadAirport($this->destination);
        $originAirport = $this->loadAirport($this->origin);
        $this->originLabel = $originAirport ? $this->loadAirportLabel($originAirport) : '';
        $this->destinationLabel = $destAirport ? $this->loadAirportLabel($destAirport) : '';
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
                'SegmentAwardQuoteForm:loadAirport'
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
                'SegmentAwardQuoteForm:loadAirportLabel'
            );
            return '';
        }
    }
}
