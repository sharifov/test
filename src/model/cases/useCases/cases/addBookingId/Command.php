<?php

namespace src\model\cases\useCases\cases\addBookingId;

/**
 * Class Command
 *
 * @property int $caseId
 * @property string|null $orderUid
 * @property int $userId
 */
class Command
{
    public int $caseId;
    public ?string $orderUid;
    public ?int $userId;

    public function __construct(
        int $caseId,
        ?string $orderUid,
        ?int $userId
    ) {
        $this->caseId = $caseId;
        $this->orderUid = $orderUid;
        $this->userId = $userId;
    }
}
