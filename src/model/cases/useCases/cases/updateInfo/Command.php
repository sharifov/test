<?php

namespace src\model\cases\useCases\cases\updateInfo;

/**
 * Class Command
 *
 * @property int $caseId
 * @property int $depId
 * @property int $categoryId
 * @property string|null $subject
 * @property string|null $description
 * @property string|null $orderUid
 */
class Command
{
    public $caseId;
    public $depId;
    public $categoryId;
    public $subject;
    public $description;
    public $orderUid;
    public $userId;

    public function __construct(
        int $caseId,
        int $depId,
        int $categoryId,
        ?string $subject,
        ?string $description,
        ?string $orderUid,
        ?int $userId
    ) {
        $this->caseId = $caseId;
        $this->depId = $depId;
        $this->categoryId = $categoryId;
        $this->subject = $subject;
        $this->description = $description;
        $this->orderUid = $orderUid;
        $this->userId = $userId;
    }
}
