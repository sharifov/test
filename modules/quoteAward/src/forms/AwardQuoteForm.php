<?php

namespace modules\quoteAward\src\forms;

use common\models\Lead;
use modules\quoteAward\src\models\FlightAwardQuoteItem;
use modules\quoteAward\src\models\SegmentAwardQuoteItem;
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

    private Lead $lead;

    /**
     * @param Lead $lead
     * @param $config
     */
    public function __construct(Lead $lead, array $flights = [], array $segments = [], array $prices = [], $config = [])
    {
        $this->lead = $lead;

        $this->flights = array_map(function (FlightAwardQuoteItem $flight) use ($prices) {
            return new FlightAwardQuoteForm($flight, $prices);
        }, $this->getFlights($flights));

        $this->segments = array_map(function (SegmentAwardQuoteItem $segment) {
            return new SegmentAwardQuoteForm($segment);
        }, $this->getSegments($segments));

        parent::__construct($config);
    }

    protected function internalForms(): array
    {
        return ['flights', 'segments'];
    }

    public function addFlight()
    {
        $flights = $this->flights;
        $flights[] = (new FlightAwardQuoteForm((new FlightAwardQuoteItem(count($flights) + 1, $this->lead))));
        $this->flights = $flights;
    }


    public function removeFlight(int $index)
    {
        $flights = $this->flights;
        $ids = array_column($flights, 'id');
        $found_key = array_search($index, $ids);
        if (array_key_exists($found_key, $flights)) {
            unset($flights[$found_key]);
        }
        $this->flights = $flights;
    }

    private function getFlights(?array $flights = []): array
    {
        if (empty($flights)) {
            return [(new FlightAwardQuoteItem(1, $this->lead))];
        }

        $data = [];
        foreach ($flights as $key => $flight) {
            $flightItem = (new FlightAwardQuoteItem($key + 1));
            $flightItem->setAttributes($flight);
            $data[$key] = $flightItem;
        }
        return $data;
    }

    public function getFlightList(): array
    {
        $flights = [];

        foreach ($this->flights as $flight) {
            $flights[$flight->id] = 'Flight ' . $flight->id;
        }
        return $flights;
    }

    public function addSegment()
    {
        $segments = $this->segments;
        $segments[] = (new SegmentAwardQuoteForm((new SegmentAwardQuoteItem($this->lead))));
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

    private function getSegments(array $segments): array
    {
        if (empty($segments)) {
            return [(new SegmentAwardQuoteItem($this->lead))];
        }

        $data = [];
        foreach ($segments as $key => $segment) {
            $segmentItem = (new SegmentAwardQuoteItem());
            $segmentItem->setAttributes($segment);
            $data[$key] = $segmentItem;
        }
        return $data;
    }
}
