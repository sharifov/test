<?php

namespace src\model\callLog\abac\dto;

use common\models\Employee;
use src\model\callLog\entity\callLog\CallLog;

/**
 * Class CallLogRecordListenAbacDto
 * @package src\model\callLog\abac\dto
 */
class CallLogRecordListenAbacDto extends \stdClass
{
    public array $record_departments;
    public bool $is_call_owner;

    /**
     * CallLogRecordListenAbacDto constructor.
     * @param CallLog $callLog
     * @param Employee $user
     */
    public function __construct(CallLog $callLog, Employee $user)
    {
        $this->record_departments = array_map(function ($item) {
            return mb_strtolower($item);
        }, [$callLog->department->dep_name]);
        $this->is_call_owner = $callLog->isOwner($user->getPrimaryKey());
    }
}
