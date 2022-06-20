<?php

namespace src\repositories\dbDataSensitive;

use common\models\DbDataSensitiveView;
use src\repositories\AbstractBaseRepository;

class DbDataSensitiveViewRepository extends AbstractBaseRepository
{
    /**
     * @param DbDataSensitiveView $dbDataSensitiveView
     */
    public function __construct(DbDataSensitiveView $dbDataSensitiveView)
    {
        parent::__construct($dbDataSensitiveView);
    }

    /**
     * @return DbDataSensitiveView
     */
    public function getModel(): DbDataSensitiveView
    {
        return $this->model;
    }
}
