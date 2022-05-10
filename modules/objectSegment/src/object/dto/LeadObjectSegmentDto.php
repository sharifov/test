<?php

namespace modules\objectSegment\src\object\dto;

use common\models\Lead;
use modules\objectSegment\src\contracts\ObjectSegmentDtoInterface;

class LeadObjectSegmentDto implements ObjectSegmentDtoInterface
{
    public ?string $origin                = null;
    public ?int $itinerary_duration    = null; //TODO implement
    public ?int $flight_segments_count = null;
    public ?int $pax_adt_count         = null;
    public ?int $pax_chd_count         = null;
    public ?int $pax_inf_count         = null;
    public ?int $lead_project_id       = null;
    public ?int $lead_department_id    = null;
    public ?float $client_budget         = null;
    public ?string $created_dt            = null;
    private int $lead_id;


    public function __construct(Lead $lead)
    {
        $this->lead_id = $lead->id;
        $this->flight_segments_count = $lead->getleadFlightSegments()->count();
        $firstSegment                = $lead->getleadFlightSegments()->orderBy(['departure' => SORT_ASC])->one();
        if (isset($firstSegment)) {
            $this->origin = $firstSegment->origin;
        }
        $this->pax_adt_count = $lead->adults;
        $this->pax_chd_count = $lead->children;
        $this->pax_inf_count = $lead->infants;
        if (isset($lead->leadPreferences)) {
            $this->client_budget = $lead->leadPreferences->clients_budget;
        }
        $this->lead_project_id    = $lead->project_id;
        $this->lead_department_id = $lead->getDepartmentId();
        $this->created_dt         = $lead->created;
    }

    public function getEntityId(): int
    {
        return $this->lead_id;
    }
}
