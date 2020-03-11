<?php

namespace sales\model\lead\useCases\lead\import;

use Webmozart\Assert\Assert;

/**
 * Class Message
 *
 * @property int $row
 * @property int $type
 * @property int $leadId
 * @property string $message
 */
class Message
{
    private const VALID = 1;
    private const INVALID = 0;

    private $row;
    private $type;
    private $leadId;
    private $message;

    public static function createValid(int $row, int $leadId): self
    {
        return new self($row, self::VALID, $leadId, null);
    }

    public static function createInvalid(int $row, string $message): self
    {
        return new self($row, self::INVALID, null, $message);
    }

    private function __construct(int $row, int $type, ?int $leadId, ?string $message)
    {
        Assert::oneOf($type, [self::VALID, self::INVALID]);
        $this->row = $row;
        $this->type = $type;
        $this->leadId = $leadId;
        $this->message = $message;
    }

    public function isValid(): bool
    {
        return $this->type === self::VALID;
    }

    public function getLeadId(): int
    {
        return $this->leadId;
    }

    public function getRow(): int
    {
        return $this->row;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }
}
