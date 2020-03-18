<?php

namespace sales\model\cases\useCases\cases\api\create;

/**
 * Class Result
 *
 * @property string $caseGid
 * @property string $clientUuid
 * @property int $csId
 */
class Result
{
    public $caseGid;
    public $clientUuid;
    public $csId;

    /**
     * Result constructor.
     * @param string $caseGid
     * @param string $clientUuid
     * @param int $csId
     */
    public function __construct(string $caseGid, string $clientUuid, int $csId)
    {
        $this->caseGid = $caseGid;
        $this->clientUuid = $clientUuid;
        $this->csId = $csId;
    }
}
