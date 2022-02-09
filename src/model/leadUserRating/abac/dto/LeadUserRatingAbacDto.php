<?php

namespace src\model\leadUserRating\abac\dto;

use common\models\Lead;

class LeadUserRatingAbacDto extends \stdClass
{
    public int $status_id;
    public bool $is_owner;


    public function __construct(Lead $lead, int $userId)
    {
        $this->status_id = $lead->status;
        $this->is_owner  = $lead->employee_id === $userId;
    }
}
