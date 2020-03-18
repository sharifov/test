<?php

namespace sales\model\cases\useCases\cases\api\create;

/**
 * Class Result
 *
 * @property string $caseGid
 * @property string|null $clientUuid
 */
class Result
{
    public $caseGid;
    public $clientUuid;

    public function __construct(string $caseGid, ?string $clientUuid)
    {
        $this->caseGid = $caseGid;
        $this->clientUuid = $clientUuid ?: '';
    }
}
