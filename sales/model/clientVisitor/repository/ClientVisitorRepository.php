<?php

namespace sales\model\clientVisitor\repository;

use sales\helpers\ErrorsToStringHelper;
use sales\model\clientVisitor\entity\ClientVisitor;

/**
 * Class ClientVisitorRepository
 */
class ClientVisitorRepository
{
    public function save(ClientVisitor $model): int
    {
        if (!$model->save()) {
            throw new \RuntimeException(ErrorsToStringHelper::extractFromModel($model));
        }
        return $model->cv_id;
    }
}
