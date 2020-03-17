<?php

namespace sales\model\cases\useCases\cases\updateInfo;

/**
 * Class Command
 *
 * @property int $caseId
 * @property string $category
 * @property string|null $subject
 * @property string|null $description
 * @property string|null $orderUid
 */
class Command
{
    public $caseId;
    public $category;
    public $subject;
    public $description;
    public $orderUid;

    public function __construct(
        int $caseId,
        string $category,
        ?string $subject,
        ?string $description,
        ?string $orderUid
    )
    {
        $this->caseId = $caseId;
        $this->category = $category;
        $this->subject = $subject;
        $this->description = $description;
        $this->orderUid = $orderUid;
    }
}
