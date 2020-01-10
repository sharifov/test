<?php

namespace sales\services\lead\qcall;

/**
 * Class FindWeightParams
 *
 * @property $projectId
 * @property $statusId
 */
class FindWeightParams
{
    public $projectId;
    public $statusId;

    /**
     * @param int|null $projectId
     * @param int|null $statusId
     */
    public function __construct(?int $projectId, ?int $statusId)
    {
        $this->projectId = $projectId;
        $this->statusId = $statusId;
    }
}
