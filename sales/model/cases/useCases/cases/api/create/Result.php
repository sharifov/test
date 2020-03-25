<?php

namespace sales\model\cases\useCases\cases\api\create;

/**
 * Class Result
 *
 * @property string $caseGid
 * @property string|null $clientUuid
 * @property int $csId
 */
class Result
{
    public $caseGid;
    public $clientUuid;
    public $csId;

    public function __construct(string $caseGid, ?string $clientUuid, int $csId)
    {
        $this->caseGid = $caseGid;
        $this->clientUuid = $clientUuid ?: '';
        $this->csId = $csId;
    }
}
