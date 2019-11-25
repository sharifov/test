<?php

namespace sales\services\lead\qcall;

/**
 * Class FindWeightParams
 *
 * @property $projectId
 */
class FindWeightParams
{
    public $projectId;

    /**
     * @param int|null $projectId
     */
    public function __construct(?int $projectId)
    {
        $this->projectId = $projectId;
    }
}
