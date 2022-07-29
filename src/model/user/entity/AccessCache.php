<?php

namespace src\model\user\entity;

/**
 * Class AccessCache
 *
 * @property array $cache
 */
class AccessCache
{
    private $cache;

    public function __construct(array $cache)
    {
        $this->cache = $cache;
    }

    public function getProjects(): ?array
    {
        return $this->cache['projects'] ?? null;
    }

    public function getDepartments(): ?array
    {
        return $this->cache['departments'] ?? null;
    }

    public function getGroups(): ?array
    {
        return $this->cache['groups'] ?? null;
    }

    public function getSkill(): ?string
    {
        return $this->cache['skill'] ?? null;
    }
}
