<?php

namespace modules\objectTask\src\scenarios\statements;

use common\models\Lead;

class NoAnswerDto
{
    public ?int $status = null;
    public ?string $reason = '';
    public ?int $project = null;
    public ?string $cabin = null;

    public function __construct(Lead $lead)
    {
        $this->status = $lead->status;
        $this->reason = $lead->getLastReasonFromLeadFlow();
        $this->project = $lead->project_id;
        $this->cabin = $lead->cabin;
    }
}
