<?php

namespace sales\model\cases\useCases\cases\updateInfo;

/**
 * Class Command
 *
 * @property int $caseId
 * @property int $categoryId
 * @property string|null $subject
 * @property string|null $description
 * @property string|null $orderUid
 */
class Command
{
    public $caseId;
    public $categoryId;
    public $subject;
    public $description;
    public $orderUid;

    public function __construct(
        int $caseId,
        int $categoryId,
        ?string $subject,
        ?string $description,
        ?string $orderUid
    )
    {
        $this->caseId = $caseId;
        $this->categoryId = $categoryId;
        $this->subject = $subject;
        $this->description = $description;
        $this->orderUid = $orderUid;
    }
}
