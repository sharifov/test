<?php

namespace sales\services\internalContact;

use common\models\Department;

/**
 * Class InternalContact
 *
 * @property Department|null $department
 * @property int|null $projectId
 * @property int|null $userId
 * @property Log $log
 */
class InternalContact
{
    public $department;
    public $projectId;
    public $userId;
    private $log;

    /**
     * @param Department|null $department
     * @param int|null $projectId
     * @param int|null $userId
     * @param Log $log
     */
    public function __construct(?Department $department, ?int $projectId, ?int $userId, Log $log)
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
        $this->log->add($message);
    }

    /**
     * @param string $prefix
     * @param string $category
     */
    public function releaseLog(?string $prefix, ?string $category): void
    {
      $this->log->release($prefix, $category);
    }
}
