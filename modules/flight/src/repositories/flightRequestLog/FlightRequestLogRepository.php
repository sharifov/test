<?php

namespace modules\flight\src\repositories\flightRequestLog;

use modules\flight\models\FlightRequestLog;
use sales\helpers\ErrorsToStringHelper;

/**
 * Class FlightRequestLogRepository
 */
class FlightRequestLogRepository
{
    public function save(FlightRequestLog $model): FlightRequestLog
    {
        if (!$model->save(false)) {
            throw new \RuntimeException(ErrorsToStringHelper::extractFromModel($model));
        }
        return $model;
    }
}
