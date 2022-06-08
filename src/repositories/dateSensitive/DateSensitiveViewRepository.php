<?php

namespace src\repositories\dateSensitive;

use common\models\DateSensitiveView;
use src\repositories\AbstractBaseRepository;

class DateSensitiveViewRepository extends AbstractBaseRepository
{
    /**
     * @param DateSensitiveView $dateSensitiveView
     */
    public function __construct(DateSensitiveView $dateSensitiveView)
    {
        parent::__construct($dateSensitiveView);
    }

    /**
     * @return DateSensitiveView
     */
    public function getModel(): DateSensitiveView
    {
        return $this->model;
    }
}
