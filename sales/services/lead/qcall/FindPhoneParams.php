<?php

namespace sales\services\lead\qcall;

/**
 * Class FindPhoneParams
 *
 * @property int|null $projectId
 * @property int|null $departmentId
 * @property int|null $leadId
 */
class FindPhoneParams
{
    public $projectId;
    public $departmentId;
    public $leadId;

    /**
     * @param int|null $projectId
     * @param int|null $departmentId
     * @param int|null $leadId
     */
    public function __construct(?int $projectId, ?int $departmentId, ?int $leadId = null)
    {
        $this->projectId = $projectId;
        $this->departmentId = $departmentId;
        $this->leadId = $leadId;
    }
}
