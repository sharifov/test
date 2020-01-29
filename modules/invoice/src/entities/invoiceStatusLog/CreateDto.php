<?php

namespace modules\invoice\src\entities\invoiceStatusLog;

/**
 * Class CreateDto
 *
 * @property $invoiceId
 * @property $startStatusId
 * @property $endStatusId
 * @property $description
 * @property $actionId
 * @property $creatorId
 */
class CreateDto
{
    public $invoiceId;
    public $startStatusId;
    public $endStatusId;
    public $description;
    public $actionId;
    public $creatorId;

    public function __construct(
        int $invoiceId,
        ?int $startStatusId,
        int $endStatusId,
        ?string $description,
        ?int $actionId,
        ?int $creatorId
    )
    {
        $this->invoiceId = $invoiceId;
        $this->startStatusId = $startStatusId;
        $this->endStatusId = $endStatusId;
        $this->description = $description;
        $this->actionId = $actionId;
        $this->creatorId = $creatorId;
    }
}
