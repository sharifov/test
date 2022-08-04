<?php

namespace modules\taskList\src\objects\call;

use common\models\Call;
use modules\taskList\src\services\TargetObjectCallService;
use yii\helpers\ArrayHelper;

class CallTaskDTO
{
    public ?string $project_key;
    public ?int $department_id;
    public ?int $duration;
    public ?bool $call_has_client = null;
    public ?int $target_object_call_attempts = null;
    public ?int $target_object_call_completed = null;

    public function __construct(Call $call, ?string $userTaskStartDT = null, ?string $userTaskEndDT = null)
    {
        $this->department_id = $call->c_dep_id;
        $this->project_key = $call->cProject->project_key ?? null;
        $this->duration = $call->c_call_duration;
        $this->call_has_client = (bool) $call->c_client_id;

        $targetObjectCallService = (new TargetObjectCallService($call, $userTaskStartDT, $userTaskEndDT))->handleCall();
        $this->target_object_call_attempts = $targetObjectCallService->getTargetObjectCallAttempts();
        $this->target_object_call_completed = $targetObjectCallService->getTargetObjectCallCompleted();
    }
}
