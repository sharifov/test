<?php

namespace modules\lead\src\abac\dto;

use common\models\Lead;

class LeadSearchAbacDto extends \stdClass
{
    public function __construct(?Lead $lead)
    {
    }
}
