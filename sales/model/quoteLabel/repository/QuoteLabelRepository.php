<?php

namespace sales\model\quoteLabel\repository;

use sales\helpers\ErrorsToStringHelper;
use sales\model\quoteLabel\entity\QuoteLabel;

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
