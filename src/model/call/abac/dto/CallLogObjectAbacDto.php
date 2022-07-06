<?php

namespace src\model\call\abac\dto;

use common\models\Employee;
use src\model\callLog\entity\callLog\CallLog;

/**
 * Class CallLogRecordListenAbacDto
 * @package src\model\callLog\abac\dto
 */
class CallLogObjectAbacDto extends \stdClass
{
    public array $record_department;
    public bool $is_call_owner;
    public integer $type_id;
    public integer $project_id;
    public integer $category_id;
    public integer $status_id;

    /**
     * CallLogRecordListenAbacDto constructor.
     * @param CallLog $callLog
     * @param Employee $user
     */
    public function __construct(CallLog $callLog, Employee $user)
    {
        $this->record_department = mb_strtolower($callLog->department->dep_name);
        $this->is_call_owner = $callLog->isOwner($user->getPrimaryKey());
        $this->type_id = $callLog->cl_type_id;
        $this->project_id = $callLog->cl_project_id;
        $this->category_id = $callLog->cl_category_id;
        $this->status_id = $callLog->cl_status_id;
    }
}
