<?php

namespace modules\objectTask\src\scenarios\statements;

use common\models\Lead;

class NoAnswerDto
{
    public ?int $status = null;
    public ?string $reason = '';
    public ?string $project = null;
    public ?string $cabin = null;

    public function __construct(Lead $lead)
    {
        $this->status = $lead->status;
        $this->reason = $lead->getLastReasonFromLeadFlow();
        if ($lead->project !== null) {
            $this->project = $lead->project->project_key;
        }
        $this->cabin = $lead->cabin;
    }
}
