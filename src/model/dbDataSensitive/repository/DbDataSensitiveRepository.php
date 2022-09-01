<?php

namespace src\model\dbDataSensitive\repository;

use common\models\DbDataSensitive;
use src\repositories\AbstractRepositoryWithEvent;
use yii\db\ActiveRecordInterface;

/**
 * Repository for `db_data_sensitive`
 */
class DbDataSensitiveRepository extends AbstractRepositoryWithEvent
{
    public function __construct(DbDataSensitive $model)
    {
        parent::__construct($model);
    }

    public function getModel(): ActiveRecordInterface
    {
        return $this->model;
    }
}
