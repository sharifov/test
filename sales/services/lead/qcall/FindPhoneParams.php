<?php

namespace sales\services\lead\qcall;

/**
 * Class FindPhoneParams
 *
 * @property int|null $projectId
 * @property int|null $departmentId
 */
class FindPhoneParams
{
    public $projectId;
    public $departmentId;

    /**
     * @param int|null $projectId
     * @param int|null $departmentId
     */
    public function __construct(?int $projectId, ?int $departmentId)
    {
        $this->projectId = $projectId;
        $this->departmentId = $departmentId;
    }
}
