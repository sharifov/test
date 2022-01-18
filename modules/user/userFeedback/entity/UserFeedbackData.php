<?php

namespace modules\user\userFeedback\entity;

/**
 * @property-read array $frontend
 * @property-read array $backend
 */
class UserFeedbackData
{
    private array $frontend;

    private array $backend;

    public function __construct(array $frontend, array $backend)
    {
        $this->frontend = $frontend;
        $this->backend = $backend;
    }

    public function toArray(): array
    {
        return [
            'frontend' => $this->frontend,
            'backend' => $this->backend
        ];
    }
}
