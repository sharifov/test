<?php

namespace modules\flight\src\entities\flightQuoteLabel\repository;

use modules\flight\src\entities\flightQuoteLabel\FlightQuoteLabel;
use src\helpers\ErrorsToStringHelper;

/**
 * Class FlightQuoteLabelRepository
 */
class FlightQuoteLabelRepository
{
    public function save(FlightQuoteLabel $model): void
    {
        if (!$model->save()) {
            throw new \RuntimeException(ErrorsToStringHelper::extractFromModel($model));
        }
    }
}
