<?php

namespace src\model\callTerminateLog\repository;

use src\helpers\ErrorsToStringHelper;
use src\model\callTerminateLog\entity\CallTerminateLog;

/**
 * Class CallTerminateLogRepository
 */
class CallTerminateLogRepository
{
    public function save(CallTerminateLog $model): CallTerminateLog
    {
        if (!$model->save()) {
            throw new \RuntimeException(ErrorsToStringHelper::extractFromModel($model));
        }
        return $model;
    }
}
