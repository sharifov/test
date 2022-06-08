<?php

namespace src\repositories\dbDateSensitive;

use common\models\DbDateSensitiveView;
use src\repositories\AbstractBaseRepository;

class DbDateSensitiveViewRepository extends AbstractBaseRepository
{
    /**
     * @param DbDateSensitiveView $dbDateSensitiveView
     */
    public function __construct(DbDateSensitiveView $dbDateSensitiveView)
    {
        parent::__construct($dbDateSensitiveView);
    }

    /**
     * @return DbDateSensitiveView
     */
    public function getModel(): DbDateSensitiveView
    {
        return $this->model;
    }
}
