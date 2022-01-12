<?php

namespace src\model\quoteLabel\repository;

use src\helpers\ErrorsToStringHelper;
use src\model\quoteLabel\entity\QuoteLabel;

/**
 * Class QuoteLabelRepository
 */
class QuoteLabelRepository
{
    public function save(QuoteLabel $model): void
    {
        if (!$model->save()) {
            throw new \RuntimeException(ErrorsToStringHelper::extractFromModel($model));
        }
    }
}
