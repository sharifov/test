<?php

namespace src\model\cases\useCases\cases\api\create;

/**
 * Class MinifyResult
 *
 * @property string $caseGid
 * @property string|null $clientUuid
 * @property int $csId
 */
class MinifyResult
{
    public string $caseGid;
    public int $csId;

    public function __construct(string $caseGid, int $csId)
    {
        $this->caseGid = $caseGid;
        $this->csId = $csId;
    }
}
