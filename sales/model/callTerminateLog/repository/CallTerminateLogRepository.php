<?php

namespace sales\model\callTerminateLog\repository;

use sales\helpers\ErrorsToStringHelper;
use sales\model\callTerminateLog\entity\CallTerminateLog;

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
