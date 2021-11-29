<?php

namespace modules\webEngage\src\service\webEngageEventData\lead;

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
    public ?string $origin = null;
    public ?string $destination = null;
    public ?string $departureDate = null;
    public ?string $returnDate = null;
    public ?string $route = null;

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

    private function prepareEventData(): LeadEventDataService
    {
        $this->origin = $this->firstSegment->origin ?? null;
        $this->destination = $this->lastSegment->destination ?? null;
        $this->departureDate = $this->firstSegment->departure ?? null;
        $this->returnDate = null; /* TODO::  */

        return $this;
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
}
