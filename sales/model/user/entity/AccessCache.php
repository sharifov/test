<?php

namespace sales\model\user\entity;

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
}
