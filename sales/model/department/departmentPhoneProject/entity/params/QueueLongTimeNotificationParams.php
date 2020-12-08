<?php

namespace sales\model\department\departmentPhoneProject\entity\params;

/**
 * Class QueueLongTimeNotificationParams
 *
 * @property bool $enable
 * @property int $waitTime
 * @property array $roleKeys
 */
class QueueLongTimeNotificationParams
{
    public bool $enable;
    public int $waitTime;
    public array $roleKeys;

    public function __construct(array $params)
    {
        $this->enable = (bool)($params['enable'] ?? false);
        $this->waitTime = (int)($params['wait_time'] ?? 0);
        $this->roleKeys = (array)($params['role_keys'] ?? []);
    }

    public function isActive(): bool
    {
        return $this->enable && $this->waitTime && $this->roleKeys;
    }

    public function getDelay(): int
    {
        return $this->waitTime * 60;
    }
}
