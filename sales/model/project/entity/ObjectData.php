<?php

namespace sales\model\project\entity;

/**
 * Class ObjectData
 *
 * @property CaseData $case
 */
class ObjectData
{
    public CaseData $case;

    public function __construct(array $params)
    {
        $this->case = new CaseData($params['case'] ?? []);
    }
}
