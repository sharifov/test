<?php

namespace sales\model\conference\entity\aggregate;

class ParticipantStatus
{
    public const JOIN = 'join';
    public const HOLD = 'hold';
    public const UNHOLD = 'unHold';
    public const LEAVE = 'leave';

    private string $value;

    private array $values = [];

    private function __construct(string $value)
    {
        $this->value = $value;
    }

    public static function init(): self
    {
        return new self('init');
    }

    public static function byJoin(): self
    {
        return new self(self::JOIN);
    }

    public static function byLeave(): self
    {
        return new self(self::LEAVE);
    }

    public static function byHold(): self
    {
        return new self(self::HOLD);
    }

    public static function byUnHold(): self
    {
        return new self(self::UNHOLD);
    }

    public function isActive(): bool
    {
        return $this->value === self::JOIN || $this->value === self::UNHOLD;
    }

    public function join(\DateTimeImmutable $date): void
    {
        $this->value = self::JOIN;
        $this->addHistory($this->value, $date);
    }

    public function isJoin(): bool
    {
        return $this->value === self::JOIN;
    }

    public function hold(\DateTimeImmutable $date): void
    {
        $this->value = self::HOLD;
        $this->addHistory($this->value, $date);
    }

    public function isHold(): bool
    {
        return $this->value === self::HOLD;
    }

    public function unHold(\DateTimeImmutable $date): void
    {
        $this->value = self::UNHOLD;
        $this->addHistory($this->value, $date);
    }

    public function isUnHold(): bool
    {
        return $this->value === self::UNHOLD;
    }

    public function leave(\DateTimeImmutable $date): void
    {
        $this->value = self::LEAVE;
        $this->addHistory($this->value, $date);
    }

    public function isLeave(): bool
    {
        return $this->value === self::LEAVE;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function getHistory(): array
    {
        return $this->values;
    }

    private function addHistory(string $value, \DateTimeImmutable $date): void
    {
        $last = array_pop($this->values);

        if ($last) {
            if ($last['value'] === self::LEAVE) {
                $this->values[] = $last;
            } else {
                $last['finish'] = $date;
                $this->values[] = [
                    'value' => $last['value'],
                    'start' => $last['start'],
                    'finish' => $date,
                ];
            }
        }

        $this->values[] = [
            'value' => $value,
            'start' => $date,
            'finish' => $value === self::LEAVE ? $date : '',
        ];
    }
}
