<?php

namespace modules\qaTask\src\useCases\qaTask\create\lead\processingQuality;

use Webmozart\Assert\Assert;

/**
 * Class Message
 *
 * @property int $type
 * @property int $leadId
 * @property int|null $taskId
 */
class Message
{
    private const VALID = 1;
    private const INVALID = 0;

    private $type;
    private $leadId;
    private $taskId;

    public static function createValid(int $leadId, int $taskId): self
    {
        return new self(self::VALID, $leadId, $taskId);
    }

    public static function createInvalid(int $leadId): self
    {
        return new self(self::INVALID, $leadId, null);
    }

    private function __construct(int $type, int $leadId, ?int $taskId)
    {
        Assert::oneOf($type, [self::VALID, self::INVALID]);
        $this->type = $type;
        $this->leadId = $leadId;
        $this->taskId = $taskId;
    }

    public function isValid(): bool
    {
        return $this->type === self::VALID;
    }

    public function getTaskId(): ?int
    {
        return $this->taskId;
    }

    public function getLeadId(): int
    {
        return $this->leadId;
    }
}
