<?php

namespace webapi\src\response;

/**
 * Class Message
 *
 * @property string $key
 * @property $value
 * @property array $response
 */
class Message
{
    public const MESSAGE_MESSAGE = 'message';
    public const MESSAGE_STATUS = 'status';
    public const MESSAGE_STATUS_CODE = 'statusCode';

    private $key;
    private $value;

    public function __construct(string $key, $value)
    {
        if (!$key) {
            throw new \InvalidArgumentException('key must be set');
        }
        $this->key = $key;
        $this->value = $value;
    }

    public function replace($value): void
    {
        $this->value = $value;
    }

    public function add($value): void
    {
        if (is_array($this->value)) {
            if (is_array($value)) {
                $this->value = array_merge($this->value,  $value);
            } else {
                $this->value[] = $value;
            }
            return;
        }

        if (empty($this->value)) {
            $this->value = $value;
            return;
        }

//        throw new \InvalidArgumentException('cant add value, because message value is not array and not empty.');
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
        return $this->key === self::MESSAGE_STATUS;
    }

    public function isStatusCode(): bool
    {
        return $this->key === self::MESSAGE_STATUS_CODE;
    }
}
