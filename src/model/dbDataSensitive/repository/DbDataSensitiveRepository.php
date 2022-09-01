<?php

namespace src\model\dbDataSensitive\repository;

use common\models\DbDataSensitive;
use src\helpers\ErrorsToStringHelper;

/**
 * Repository for `db_data_sensitive`
 */
class DbDataSensitiveRepository
{
    /**
     * @param DbDataSensitive $model
     * @return DbDataSensitive
     */
    public function save(DbDataSensitive $model): DbDataSensitive
    {
        if (!$model->save()) {
            throw new \RuntimeException(ErrorsToStringHelper::extractFromModel($model));
        }

        return $model;
    }
}
