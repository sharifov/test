<?php

namespace src\model\dbDataSensitive\repository;

use src\model\dbDataSensitive\entity\DbDataSensitive;
use src\repositories\AbstractRepositoryWithEvent;

/**
 * Repository for `db_data_sensitive`
 *
 * @property DbDataSensitive $model
 */
class DbDataSensitiveRepository extends AbstractRepositoryWithEvent
{
    public function __construct(DbDataSensitive $model)
    {
        parent::__construct($model);
    }

    public function getModel(): DbDataSensitive
    {
        return $this->model;
    }
}
