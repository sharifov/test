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
    public ?string $lead_project_name       = null;
    public ?string $lead_department_name    = null;
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
        $this->lead_project_name    = $lead->project->project_key ?? null;
        $this->lead_department_name = $lead->lDep->dep_key ?? null;
        $this->created_dt         = $lead->created;
    }

    public function getEntityId(): int
    {
        return $this->lead_id;
    }
}
