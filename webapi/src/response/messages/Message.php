<?php

namespace webapi\src\response\messages;

/**
 * Class Message
 */
class Message
{
    public const MESSAGE_MESSAGE = 'message';
    public const STATUS_MESSAGE = 'status';
    public const STATUS_CODE_MESSAGE = 'statusCode';
    public const ERRORS_MESSAGE = 'errors';
    public const CODE_MESSAGE = 'code';
    public const TECHNICAL_MESSAGE = 'technical';
    public const REQUEST_MESSAGE = 'request';
    public const DATA_MESSAGE = 'data';
    public const SOURCE_MESSAGE = 'source';
    public const DETAIL_ERROR_MESSAGE = 'detailError';

    private $key;
    private $value;

    public function __construct(string $key, ...$values)
    {
        if (!$key) {
            throw new \InvalidArgumentException('key must be set');
        }
        $this->key = $key;
        foreach ($values as $value) {
            $this->add($value);
        }
    }

    public function replace($value): void
    {
        $process = self::processValue($value);
        if (is_array($process)) {
            $this->removeValue();
            $this->createArrayValue();
            $this->addToArray($process);
            return;
        }
        $this->value = $process;
    }

    public function add($value): void
    {
        if ($value === null) {
            return;
        }

        if ($this->isEqual($value)) {
            $value = self::processEqualValue($value);
        } elseif (!$this->value) {
            $this->replace($value);
            return;
        } else {
            $value = self::processValue($value);
        }

        $oldValue = $this->value;
        $this->removeValue();
        $this->createArrayValue();
        $this->addToArray($oldValue);
        $this->addToArray($value);
    }

    public function getResponse(): array
    {
        return [$this->key => $this->value];
    }

    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    public function isMessage(): bool
    {
        return $this->key === self::MESSAGE_MESSAGE;
    }

    public function isStatus(): bool
    {
        return $this->key === self::STATUS_MESSAGE;
    }

    public function isStatusCode(): bool
    {
        return $this->key === self::STATUS_CODE_MESSAGE;
    }

    public function isErrors(): bool
    {
        return $this->key === self::ERRORS_MESSAGE;
    }

    public function isCode(): bool
    {
        return $this->key === self::CODE_MESSAGE;
    }

    public function isTechnical(): bool
    {
        return $this->key === self::TECHNICAL_MESSAGE;
    }

    public function isRequest(): bool
    {
        return $this->key === self::REQUEST_MESSAGE;
    }

    public function isData(): bool
    {
        return $this->key === self::DATA_MESSAGE;
    }

    private function createArrayValue(): void
    {
        $this->value = [];
    }

    private function addToArray($value): void
    {
        if ($value === null) {
            return;
        }

        if (is_array($value)) {
            foreach ($value as $k => $v) {
                $this->value[$k] = self::processValue($v);
            }
//            $this->value = array_merge($this->value, $value);
        } else {
            $this->value[] = $value;
        }
    }

    private function isEqual($value): bool
    {
        return is_object($value) && is_a($value, self::class);
    }

    private static function processEqualValue(Message $value): array
    {
        return [$value->getKey() => self::processValue($value->getValue())];
    }

    private static function processValue($value)
    {
        /** @var MessageValue $value */
        return (is_object($value) && is_a($value, MessageValue::class)) ? $value->getData() : $value;
    }

    private function removeValue(): void
    {
        $this->value = null;
    }
}
