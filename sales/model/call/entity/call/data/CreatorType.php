<?php

namespace sales\model\call\entity\call\data;

/**
 * Class CreatorType
 *
 * @property $id
 */
class CreatorType
{
    public const UNDEFINED = 0;
    public const AGENT = 1;
    public const CLIENT = 2;
    public const USER = 3;

    public const LIST = [
        self::UNDEFINED => 'Undefined',
        self::AGENT => 'Agent',
        self::CLIENT => 'Client',
        self::USER => 'User',
    ];

    public int $id;

    public function __construct(array $data)
    {
        $this->id = (int)($data['id'] ?? self::UNDEFINED);
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
        ];
    }

    public function isAgent(): bool
    {
        return $this->id === self::AGENT;
    }

    public function isClient(): bool
    {
        return $this->id === self::CLIENT;
    }

    public function isUser(): bool
    {
        return $this->id === self::USER;
    }
}
