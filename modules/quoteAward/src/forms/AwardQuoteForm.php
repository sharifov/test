<?php

namespace modules\quoteAward\src\forms;

use common\models\Employee;
use common\models\Lead;
use modules\flight\models\Flight;
use modules\quoteAward\src\models\FlightAwardQuoteItem;
use modules\quoteAward\src\models\SegmentAwardQuoteItem;
use src\auth\Auth;
use src\forms\CompositeForm;
use yii\helpers\ArrayHelper;

/**
 * @property FlightAwardQuoteForm[] $flights
 * @property SegmentAwardQuoteForm[] $segments
 */
class AwardQuoteForm extends CompositeForm
{
    public const REQUEST_FLIGHT = 'flight';
    public const REQUEST_SEGMENT = 'segment';
    public const REQUEST_TRIP = 'trip';

    private Lead $lead;

    public $trip_type;
    public $cabin;
    public $checkPayment;
    public $employee_id;
    public $labels;

    /**
     * @param Lead $lead
     * @param $config
     */
    public function __construct(Lead $lead, array $flights = [], array $segments = [], array $prices = [], $config = [])
    {
        $this->lead = $lead;
        $this->trip_type = $lead->trip_type;
        $this->cabin = $lead->cabin ?: Lead::TRIP_TYPE_ONE_WAY;
        $this->employee_id = Auth::id();
        $this->checkPayment = true;

        $this->flights = array_map(function (FlightAwardQuoteItem $flight) use ($prices) {
            return new FlightAwardQuoteForm($flight, $prices);
        }, $this->getFlights($flights));

        $this->segments = array_map(function (SegmentAwardQuoteItem $segment) {
            return new SegmentAwardQuoteForm($segment);
        }, $this->getSegments($segments));

        parent::__construct($config);
    }

    public function rules(): array
    {
        return [
            [['trip_type', 'cabin'], 'required'],
            [['labels', 'checkPayment'], 'safe'],
            ['checkPayment', 'boolean'],
            [['employee_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['employee_id' => 'id']],
            ['trip_type', 'in', 'range' => array_keys(Lead::getFlightTypeList())],
            ['cabin', 'string', 'max' => 1],
            ['cabin', 'in', 'range' => array_keys(Flight::getCabinClassList())],
        ];
    }

    protected function internalForms(): array
    {
        return ['flights', 'segments'];
    }

    public function addFlight()
    {
        $flights = $this->flights;
        $flights[] = (new FlightAwardQuoteForm((new FlightAwardQuoteItem(count($flights), $this->lead))));
        $this->flights = $flights;
    }


    public function removeFlight(int $index)
    {
        $flights = $this->flights;

        if (array_key_exists($index, $flights)) {
            unset($flights[$index]);
        }
        $i = 0;
        $flightList = [];
        foreach ($flights as $flight) {
            $flight->id = $i;
            $flightList[$i] = $flight;
            $i++;
        }

        $this->flights = $flightList;
    }

    private function getFlights(?array $flights = []): array
    {
        if (empty($flights)) {
            return [(new FlightAwardQuoteItem(0, $this->lead))];
        }

        $data = [];
        foreach ($flights as $key => $flight) {
            $flightItem = (new FlightAwardQuoteItem($key));
            $flightItem->setAttributes($flight);
            $data[$key] = $flightItem;
        }
        return $data;
    }

    public function getFlightList(): array
    {
        $flights = [];

        foreach ($this->flights as $flight) {
            $flights[$flight->id] = 'Flight ' . ($flight->id + 1);
        }
        return $flights;
    }

    public function addSegment($tripId)
    {
        $segments = $this->segments;
        $segments[] = (new SegmentAwardQuoteForm((new SegmentAwardQuoteItem($this->lead, $tripId))));
        $this->segments = $segments;
    }


    public function removeSegment(int $index)
    {
        $segments = $this->segments;
        if (array_key_exists($index, $segments)) {
            unset($segments[$index]);
        }
        $this->segments = $segments;
    }

    public function removeTrip(int $removeIndex)
    {
        $segments = $this->segments;
        foreach ($segments as $key => $segment) {
            if ($removeIndex === (int)$segment->trip) {
                unset($segments[$key]);
            }
        }
        $this->segments = $segments;
    }

    private function getSegments(array $segments): array
    {
        if (empty($segments)) {
            return [(new SegmentAwardQuoteItem($this->lead))];
        }

        $data = [];
        foreach ($segments as $key => $segment) {
            $segmentItem = (new SegmentAwardQuoteItem());
            $segmentItem->setParams($segment);
            $data[$key] = $segmentItem;
        }
        return $data;
    }

    public function attributeLabels()
    {
        return [
            'employee_id' => 'Quote Creator',
            'labels' => 'Quote label'
        ];
    }

    public function groupByTrip(): array
    {
        $trips = [];
        foreach ($this->segments as $key => $segment) {
            $trips[$segment->trip][$key] = $segment;
        }

        return $trips;
    }

    public function getTotalPrice(): array
    {
        $totalPrice = [
            'selling' => 0,
            'net' => 0,
            'fare' => 0,
            'taxes' => 0,
            'markUp' => 0,
        ];

        foreach ($this->flights as $flight) {
            $prices = $flight->getTotalPrice();
            $totalPrice['selling'] += $prices['selling'];
            $totalPrice['net'] += $prices['net'];
            $totalPrice['fare'] += $prices['fare'];
            $totalPrice['taxes'] += $prices['taxes'];
            $totalPrice['markUp'] += $prices['markUp'];
        }

        return $totalPrice;
    }
}
