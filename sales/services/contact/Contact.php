<?php

namespace sales\services\contact;

use common\models\Department;

/**
 * Class Contact
 *
 * @property Department|null $department
 * @property int|null $projectId
 * @property int|null $userId
 * @property array $log
 */
class Contact
{
    public $department;
    public $projectId;
    public $userId;
    private $log;

    /**
     * @param Department|null $department
     * @param int|null $projectId
     * @param int|null $userId
     * @param array $log
     */
    public function __construct(?Department $department, ?int $projectId, ?int $userId, array $log)
    {
        $this->department = $department;
        $this->projectId = $projectId;
        $this->userId = $userId;
        $this->log = $log;
    }

    /**
     * @param int $projectId
     */
    public function replaceProject(?int $projectId): void
    {
        $this->projectId = $projectId;
    }

    /**
     * @param string $message
     */
    public function addLog(string $message): void
    {
        $this->log[] = $message;
    }

    /**
     * @param string $prefix
     * @param string $category
     */
    public function releaseLog(string $prefix, string $category): void
    {
        foreach ($this->log as $log) {
            \Yii::error($prefix . ' | ' . $log, $category);
        }
        $this->log = [];
    }
}
