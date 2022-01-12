<?php

namespace src\model\leadData\abac\dto;

use src\model\leadData\entity\LeadData;

/**
 * Class LeadDataAbacDto
 *
 * @property string $dataKey;
 */
class LeadDataAbacDto extends \stdClass
{
    public string $dataKey;

    public function __construct(LeadData $leadData)
    {
        $this->dataKey = $leadData->ld_field_key;
    }
}
