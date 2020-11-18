<?php

namespace sales\model\department\department;

/**
 * Class ObjectSettings
 *
 * @property Type $type
 * @property LeadSettings $lead
 * @property CaseSettings $case
 */
class ObjectSettings
{
    public Type $type;
    public LeadSettings $lead;
    public CaseSettings $case;

    public function __construct(array $params)
    {
        $this->type = new Type($params['type']);
        $this->lead = new LeadSettings($params['lead']);
        $this->case = new CaseSettings($params['case']);
    }
}
