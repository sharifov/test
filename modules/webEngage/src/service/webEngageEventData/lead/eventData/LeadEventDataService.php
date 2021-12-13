<?php

namespace modules\webEngage\src\service\webEngageEventData\lead\eventData;

use common\models\Lead;
use common\models\LeadFlightSegment;
use common\models\QuoteSegment;

/**
 * Class LeadEventDataService
 *
 * @property string|null $origin
 * @property string|null $destination
 * @property string|null $departureDate
 * @property string|null $returnDate
 * @property string|null $route
 *
 * @property Lead $lead
 * @property LeadFlightSegment|null $firstSegment
 * @property LeadFlightSegment|null $lastSegment
 */
class LeadEventDataService
{
    private ?string $origin = null;
    private ?string $destination = null;
    private ?string $departureDate = null;
    private ?string $returnDate = null;
    private ?string $route = null;

    private Lead $lead;
    private ?LeadFlightSegment $firstSegment = null;
    private ?LeadFlightSegment $lastSegment = null;

    /**
     * @param Lead $lead
     */
    public function __construct(Lead $lead)
    {
        $this->lead = $lead;
        $this->prepareSegments()
            ->prepareEventData();
    }

    private function prepareSegments(): LeadEventDataService
    {
        if ($leadFlightSegments = $this->lead->leadFlightSegments ?? null) {
            $route = [];
            foreach ($leadFlightSegments as $key => $segment) {
                if ($key === 0) {
                    $route[] = $segment->origin;
                    $route[] = $segment->destination;
                    continue;
                }
                if ($segment->origin !== end($route)) {
                    $route[] = $segment->origin;
                }
                if ($segment->destination !== end($route)) {
                    $route[] = $segment->destination;
                }
            }
            $this->route = implode('-', $route);
            $this->firstSegment = $leadFlightSegments[0];
            $this->lastSegment = end($leadFlightSegments);
            reset($leadFlightSegments);
        }
        return $this;
    }

    private function prepareEventData(): LeadEventDataService
    {
        $this->origin = $this->firstSegment->origin ?? null;
        $this->destination = $this->firstSegment->destination ?? null;
        $this->departureDate = $this->firstSegment->departure ?? null;
        $this->returnDate = null;
        if ($this->lead->trip_type === Lead::TRIP_TYPE_ROUND_TRIP) {
            $this->returnDate = $this->lastSegment->departure ?? null;
        }

        return $this;
    }

    public function getRoute(): ?string
    {
        return $this->route;
    }

    public function getReturnDate(): ?string
    {
        return $this->returnDate;
    }

    public function getOrigin(): ?string
    {
        return $this->origin;
    }

    public function getDestination(): ?string
    {
        return $this->destination;
    }

    public function getDepartureDate(): ?string
    {
        return $this->departureDate;
    }
}
