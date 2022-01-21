<?php

namespace src\model\clientVisitor\repository;

use src\helpers\ErrorsToStringHelper;
use src\model\clientVisitor\entity\ClientVisitor;

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
